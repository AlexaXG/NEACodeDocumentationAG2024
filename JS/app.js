
//Multiple API keys from different accounts to ensure queries can still be run during the NEA development period

//const apiKey = "a92238f287be4371a6b190852c0be1b5"; 
//first account
//const apiKey = "342899e01df24fd79ae66ccd8fcb542d"; 
//recipeAPI1@gmail.com
const apiKey = "912595cf052c4231ac1e2528628d9d09";
//recipeAPI2@gmail.com
//const apiKey ="9d323fbea5be46e5b6872aaf660584f7";
let recipeIds = [];

function addStylesheet(url) {
    const link = document.createElement('link');
    link.rel = 'stylesheet';
    link.href = url;
    document.head.appendChild(link);
}
addStylesheet('/css/pageStyling.css');
const SelectedMealType = document.getElementById("BLDChoice");
const selectedValue = SelectedMealType.value;
	console.log(selectedValue);



async function searchRecipesByName() {

	const query = document.getElementById("userInput").value;

	if (!query) {
		document.getElementById("errorMessage").innerHTML =
			"Must enter a query";
		return;
		
	}
	var apiQueryingUrl = `https://api.spoonacular.com/recipes/complexSearch?query=${encodeURIComponent(query)}&apiKey=${apiKey}&number=3`;
		if (usePreferenceCheck() === false) {
		} else {
			queryURLBuilder = queryURLBuilder + `&diet=${encodeURIComponent(userPreference)}`;
		}
		if (selectedValue == "") {}
		else {
			apiQueryingUrl = apiQueryingUrl + `&type=${encodeURIComponent(selectedValue)}`;
		}
	console.log("URL:" + apiQueryingUrl);

	try {
		const response = await fetch(apiQueryingUrl);
		const data = await response.json();
		storeRecipeIds(data.results);
	} catch (error) {
		document.getElementById("errorMessage").innerHTML =
			"error getting data from API";
	}
}

async function searchRecommendedRecipes() {
	//add allergies
	var selectedMealType = document.getElementById("BLDChoice").value;
	var queryURLBuilder = `https://api.spoonacular.com/recipes/random?apiKey=${apiKey}&number=2`;

    if (usePreferenceCheck() === false) {
    } else {
		queryURLBuilder = queryURLBuilder + `&include-tags=${encodeURIComponent(userPreference)}`;
	}
	stringOfAllergies = "";
	if (allergyCheck() === true) {
		for (let i = 0; i < userAllergies.length; i++) {
			stringOfAllergies += userAllergies[i] + ",";
		}
		stringOfAllergies = stringOfAllergies.slice(0, -1);
		console.log("SOA:" . stringOfAllergies);
		queryURLBuilder = queryURLBuilder + `&exclude-tags=${encodeURIComponent(stringOfAllergies)}`;
	}
    
	var apiQueryingUrl = queryURLBuilder;
	try {
		const response = await fetch(apiQueryingUrl);
		const data = await response.json();
		console.log("full data: " . data);
		if (data.recipes && Array.isArray(data.recipes)) {
            storeRecipeIds(data.recipes);
        } else {
            console.error("expected array: ", data.recipes);
        }
	} catch (error) {
		document.getElementById("errorMessage").innerHTML = error;
	}    
} 
function allergyCheck() {
	if (userAllergies.length > 0) {
		return true;
	}
	else {
		return false;
	}
}
function usePreferenceCheck() {
    if (userPreference === 'No Preference') {
       return false;
    } 
}





async function getRecipeInformation(idsString) {
		const apiQueryingUrl = `https://api.spoonacular.com/recipes/informationBulk?ids=${encodeURIComponent(idsString)}&apiKey=${apiKey}&includeNutrition=true`;
		try {
			const response = await fetch(apiQueryingUrl);
			const data = await response.json();
            displayResultsHere(data);

		} catch (error) {
			document.getElementById("errorMessage").innerHTML = error;
		}
}

function displayResultsHere(recipes) {
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
		<h2>${recipe.title}</h2>
		<img src="${recipe.image}" id="recipeImg" draggable="false" onerror="imagePostError(this)" width="115">
		<div class="recipeButtons">
		<a class="urlButton" href="${recipe.sourceUrl}" target="_blank">
		<button type="button" id="recipeLink">Recipe Link</button></a>
		<a class="favButton" onclick="saveThisRecipe(${recipe.id})">
		<img class="favImg" id=${recipe.id} src="/Other Files/heart-empty.svg" width="35px" draggable="false"></a>
		</div>
        `;
		resultsDiv.appendChild(recipeElement);
	});
}

//add recipe id to database
function saveThisRecipe(recipeID) {
	const svgData = document.getElementById(recipeID);
	const emptyHeart = "/Other Files/heart-empty.svg";
	const fullHeart = "/Other Files/heart-full.svg";
	svgData.src = svgData.src.includes("heart-empty.svg") ? fullHeart : emptyHeart;
}

function imagePostError(image) {
	image.src = "";
	image.alt = "No image available";
}
// function storeRecipeIds(recipes) {
// 	recipeIds = [];
// 	recipes.forEach((recipe) => {
// 		recipeIds.push(recipe.id);
// 	});
//     const idsString = recipeIds.join(",");
//     console.log("ID's:", idsString);
//     getRecipeInformation(idsString);
// }
function storeRecipeIds(recipes) {
    if (!Array.isArray(recipes)) {
        console.error("Expected an array of recipes, but got:", recipes);
        return;
    }
    recipeIds = recipes.map(recipe => recipe.id);
    const idsString = recipeIds.join(",");
    console.log("ID's:", idsString);
    getRecipeInformation(idsString);
}


