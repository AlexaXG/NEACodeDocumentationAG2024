
//Multiple API keys from different accounts to ensure queries can still be run during the NEA development 

//const apiKey ="9d323fbea5be46e5b6872aaf660584f7";
//const apiKey = "a92238f287be4371a6b190852c0be1b5"; 
//const apiKey = "342899e01df24fd79ae66ccd8fcb542d"; 
const apiKey = "912595cf052c4231ac1e2528628d9d09";

let recipeIds = [];

const link = document.createElement('link');
link.rel = 'stylesheet';
link.href = '/css/pageStyling.css';
document.head.appendChild(link);


async function searchRecipesByName() {
	const query = document.getElementById("userInput").value;

	if (!query) {
		document.getElementById("errorMessage").innerHTML =
			"Must enter a query";
		return;
	}
	var apiQueryingUrl = `https://api.spoonacular.com/recipes/complexSearch?query=${encodeURIComponent(query)}&apiKey=${apiKey}&number=3&addRecipeNutrition=true`;
	//console.log("URL:" + apiQueryingUrl);
	if (!doesUserHaveNoPreference()) {
		apiQueryingUrl = apiQueryingUrl + `&diet=${encodeURIComponent(userPreference)}`;
	}
	try {
		const response = await fetch(apiQueryingUrl);
        if (response.ok) {
            const data = await response.json();
            if (data.results && Array.isArray(data.results)) {
		        displayResultsHere(data.results);
		        sendRecipeToDatabase(data.results);
            } else {
                console.error("data results error");
            }
        } else {
            console.log("response error");
        }
	} catch (error) {
		document.getElementById("errorMessage").innerHTML =
			"Error getting data from API: " + error.message;
	}
}

let callCounter = parseInt(localStorage.getItem('callCounter')) || 0;

async function submitMealChoiceAndSearchRecipes() {
    const selectedMealChoice = document.getElementById("mealChoice").value;
    let meals;
    if (callCounter % 2 === 0) {
        console.log('Database call');
        meals = await searchFromDatabase(selectedMealChoice);
    } else {
        console.log('API call');
        meals = await searchRecommendedRecipes(selectedMealChoice);
    }
    callCounter++;
    localStorage.setItem('callCounter', callCounter);

    return meals;
}

async function searchFromDatabase(mealChoice) {
    try {
        let databaseDataReceived = [];
        let callAttempts = 0;

        while (callAttempts < 5) {
            const databaseResponse = await fetch(`http://localhost/PHP/searchMealFromDatabase.php?mealType=${encodeURIComponent(mealChoice)}&userID=${encodeURIComponent(userID)}&preference=${encodeURIComponent(userPreference)}`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                },
            });
            databaseDataReceived = await databaseResponse.json();

            if (databaseDataReceived.length > 0) {
				if (allergyCheck() === 'false') {
					displayResultsHereForDatabaseMeals(databaseDataReceived);
				} else {
                	const allergenSafeMeals = await ensureMealsExcludeAllergens(databaseDataReceived);

                	if (allergenSafeMeals.length > 0) {
                    	console.log('Allergen-safe meals from database:', allergenSafeMeals);
                    	displayResultsHereForDatabaseMeals(allergenSafeMeals);
                    	return;
                	} else {
                    	console.log('No allergen-safe meals found');
                	}
				}
            } else {
                console.log('Database exhausted, switching to API');
                return searchRecommendedRecipes(mealChoice);
            }
            callAttempts++;
        }

        console.log('Unable to find allergen-safe meals.');
        return searchRecommendedRecipes(mealChoice);

    } catch (error) {
        console.error('Error during database meal search:', error);
        return [];
    }
}
async function ensureMealsExcludeAllergens(databaseDataReceived) {
    try {
        const allergenSafeMeals = [];

        for (const meal of databaseDataReceived) {
            const mealID = meal.MealID;

            const ingredientResponse = await fetch(`https://api.spoonacular.com/recipes/${mealID}/ingredientWidget.json?apiKey=${apiKey}`);
            const ingredientData = await ingredientResponse.json();

            const ingredients = ingredientData.ingredients.map(ingredient => ingredient.name.toLowerCase());

            const hasAllergens = userAllergies.some(allergen => ingredients.some(ingredient => ingredient.includes(allergen.toLowerCase()))
            );

            if (!hasAllergens) {
                allergenSafeMeals.push(meal);
            }
        }
        return allergenSafeMeals;

    } catch (error) {
        console.error('Error with allergies', error);
        return [];
    }
}

async function searchFavouritedMeals() {
	try {
        let databaseDataReceived = [];
            const databaseResponse = await fetch(`http://localhost/PHP/searchFavouritedMeals.php?userID=${encodeURIComponent(userID)}`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                },
            });
            const favouritedMealsData = await databaseResponse.json();
            if (favouritedMealsData.success && favouritedMealsData.meals.length > 0) {
				displayFavouritedMeals(favouritedMealsData.meals);
                return;
            } else {
                const resultsDiv = document.getElementById("displayResultsHere");
				resultsDiv.innerHTML = "No favourited meals found";
            }
    } catch (error) {
        console.error('Error during favourite meal fetch:', error);
        return [];
    }
}

async function searchRecommendedRecipes(selectedMealChoice) {
	var queryURLBuilder = `https://api.spoonacular.com/recipes/complexSearch?apiKey=${apiKey}&number=3&sort=random&addRecipeNutrition=true`;

    if (!doesUserHaveNoPreference()) {
		queryURLBuilder = queryURLBuilder + `&diet=${encodeURIComponent(userPreference)}`;
	}
	if (selectedMealChoice !== "Any") {
		queryURLBuilder = queryURLBuilder + `&type=${encodeURIComponent(selectedMealChoice)}`;
	}
	var stringOfAllergies = "";
	if (allergyCheck()) {
		stringOfAllergies = userAllergies.join(",");
		//console.log("String of allergies: " + stringOfAllergies);
		queryURLBuilder = queryURLBuilder + `&excludeIngredients=${encodeURIComponent(stringOfAllergies)}`;
		console.log(queryURLBuilder);
	}
	var apiQueryingUrl = queryURLBuilder;


	try {
		const response = await fetch(apiQueryingUrl);
		const data = await response.json();
		if (data.results && Array.isArray(data.results)) {
            displayResultsHere(data.results);
			sendRecipeToDatabase(data.results, userID);
        } else {
            console.error("expected array: ", data.results);
        }
	} catch (error) {
		document.getElementById("errorMessage").innerHTML = error;
	}    
} 
function allergyCheck() {
	if (userAllergies.length > 0) {
		return true;
	}
	return false;
}
function doesUserHaveNoPreference() {
    if (userPreference === 'No Preference') {
       return true;
    } 
}

async function sendRecipeToDatabase(recipes, userID) {
	for (const recipe of recipes) {
        const mealData = {
            MealID: recipe.id,  
            MealName: recipe.title,
            RecipeURL: recipe.sourceUrl,
            RecipeImg: recipe.image, 
            Protein: recipe.nutrition?.nutrients?.find(n => n.name === "Protein")?.amount || 0,
            Fats: recipe.nutrition?.nutrients?.find(n => n.name === "Fat")?.amount || 0,
            Carbs: recipe.nutrition?.nutrients?.find(n => n.name === "Carbohydrates")?.amount || 0,
            MealTypes: recipe.dishTypes,
			UserID: userID,
			Preferences: recipe.diets
        }; 
        try {
			//console.log("data:", mealData);
            const response = await fetch('http://localhost/PHP/saveMealToDatabase.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(mealData),
            });
            if (response.ok) {
			} else {
				console.error("Meal wasnt inserted");
			}
        } catch (error) {
            console.error("Error sending data", error);
        }
    }
}

function displayResultsHereForDatabaseMeals(recipes) {
	const resultsDiv = document.getElementById("displayResultsHere");
	resultsDiv.innerHTML = "";

	if (recipes.length === 0) {
		resultsDiv.innerHTML = "No recipes found";
		return;
	}
	recipes.forEach((recipe) => {
		const recipeElement = document.createElement("div");
        recipeElement.classList.add("recipeBox");
		recipeElement.innerHTML = `
		<h2>${recipe.MealName}</h2>
		<img src="${recipe.RecipeImg}" id="recipeImg" draggable="false" onerror="imagePostError(this)" width="115">
		<div class="macros">
            <p>Protein: ${recipe.Protein}g</p>
            <p>Fats: ${recipe.Fats}g</p>
            <p>Carbs: ${recipe.Carbs}g</p>
        </div>
		<div class="recipeButtons">
		<a class="urlButton" href="${recipe.RecipeURL}" target="_blank">
		<button type="button" id="recipeLink">Recipe Link</button></a>
		<a class="favButton" onclick="addOrRemoveRecipeFromFavourites(${recipe.MealID})">
		<img class="favImg" id=${recipe.MealID} src="/Other Files/heart-empty.svg" width="35px" draggable="false"></a>
		</div>
        `;
		resultsDiv.appendChild(recipeElement);
	});

}
function displayResultsHere(recipes) {
	const resultsDiv = document.getElementById("displayResultsHere");
	resultsDiv.innerHTML = "";

	if (recipes.length === 0) {
		resultsDiv.innerHTML = "No recipes found";
		return;
	}
	recipes.forEach((recipe) => {
		const Protein = recipe.nutrition.nutrients?.find(nutrient => nutrient.name === "Protein")?.amount;
        const Fats = recipe.nutrition.nutrients?.find(nutrient => nutrient.name === "Fat")?.amount;
        const Carbs = recipe.nutrition.nutrients?.find(nutrient => nutrient.name === "Carbohydrates")?.amount;
		const recipeElement = document.createElement("div");
        recipeElement.classList.add("recipeBox");
		recipeElement.innerHTML = `
        <h2>${recipe.title}</h2>
        <img src="${recipe.image}" id="recipeImg" draggable="false" onerror="imagePostError(this)" width="115">
        <div class="macros">
            <p>Protein: ${Protein}g</p>
            <p>Fats: ${Fats}g</p>
            <p>Carbs: ${Carbs}g</p>
        </div>
        <div class="recipeButtons">
            <a class="urlButton" href="${recipe.sourceUrl}" target="_blank">
                <button type="button" id="recipeLink">Recipe Link</button>
            </a>
            <a class="favButton" onclick="addOrRemoveRecipeFromFavourites(${recipe.id})">
                <img class="favImg" id=${recipe.id} src="/Other Files/heart-empty.svg" width="35px" draggable="false">
            </a>
        </div>
    `;
		resultsDiv.appendChild(recipeElement);
	});
}

async function addOrRemoveRecipeFromFavourites(mealID) {
    const svgData = document.getElementById(mealID);
    const emptyHeart = "/Other Files/heart-empty.svg";
    const fullHeart = "/Other Files/heart-full.svg";
    const isFavourite = svgData.src.includes("heart-empty.svg");
    svgData.src = isFavourite ? fullHeart : emptyHeart;

    try {
        if (isFavourite) {
            const response = await fetch('http://localhost/PHP/addMealToFavourites.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ mealID: mealID, userID: userID }),
            });
            if (!response.ok) {
                console.error("Meal wasnt added");
                svgData.src = emptyHeart;
            }
        } else {
            const response = await fetch('http://localhost/PHP/removeMealFromFavourites.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ mealID: mealID, userID: userID }),
            });
            if (!response.ok) {
                console.error("Meal wasn't removed");
                svgData.src = fullHeart;
            }
        }
    } catch (error) {
        console.error("error updating favourites:", error);
        svgData.src = isFavourite ? emptyHeart : fullHeart;
    }
}

function displayFavouritedMeals(recipes) {
	const resultsDiv = document.getElementById("displayResultsHere");
	resultsDiv.innerHTML = "";

	if (recipes.length === 0) {
		resultsDiv.innerHTML = "No recipes found";
		return;
	}
	recipes.forEach((recipe) => {
		const recipeElement = document.createElement("div");
        recipeElement.classList.add("recipeBox");
		recipeElement.innerHTML = `
		<h2>${recipe.MealName}</h2>
		<img src="${recipe.RecipeImg}" id="recipeImg" draggable="false" onerror="imagePostError(this)" width="115">
		<div class="macros">
            <p>Protein: ${recipe.Protein}g</p>
            <p>Fats: ${recipe.Fats}g</p>
            <p>Carbs: ${recipe.Carbs}g</p>
        </div>
		<div class="recipeButtons">
		<a class="urlButton" href="${recipe.RecipeURL}" target="_blank">
		<button type="button" id="recipeLink">Recipe Link</button></a>
		<a class="favButton" onclick="addOrRemoveRecipeFromFavourites(${recipe.MealID})">
		<img class="favImg" id=${recipe.MealID} src="/Other Files/heart-full.svg" width="35px" draggable="false"></a>
		</div>
        `;
		resultsDiv.appendChild(recipeElement);
	});
}




