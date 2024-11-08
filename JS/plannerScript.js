const activities = []; // This will hold the activity objects

function createActivity(name, day, startTime, endTime) {
    const activity = { name, day, startTime, endTime };
    activities.push(activity);
    renderActivities();
}

function renderActivities() {
    const grid = document.querySelector('.grid');
    grid.innerHTML = ''; 

    const daysOfWeek = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
    const timeSlots = Array.from({ length: 24 }, (_, i) => `${String(i).padStart(2, '0')}:00`); 

    timeSlots.forEach(time => {
        const row = document.createElement('div');
        row.classList.add('time-slot');


        daysOfWeek.forEach(day => {
            const dayDiv = document.createElement('div');
            dayDiv.classList.add('day');
            dayDiv.style.position = 'relative';

        
            activities.forEach(activity => {
                if (activity.day === day || (activity.startTime > activity.endTime && activity.day === day)) {
                    const startHour = parseInt(activity.startTime.split(':')[0], 10);
                    const startMinute = parseInt(activity.startTime.split(':')[1], 10);
                    const endHour = parseInt(activity.endTime.split(':')[0], 10);
                    const endMinute = parseInt(activity.endTime.split(':')[1], 10);

                    const startInMinutes = startHour * 60 + startMinute;
                    const endInMinutes = endHour * 60 + endMinute;

    
                    if (activity.startTime > activity.endTime && activity.day === day) {
                       
                        const height = (60 - (startInMinutes % 60)) + ((endInMinutes - (24 * 60)) % 60);
                        const top = (startInMinutes % 60) + 'px';
                        const activityDiv = document.createElement('div');
                        activityDiv.classList.add('activity');
                        activityDiv.style.height = height + 'px';
                        activityDiv.style.top = top;
                        activityDiv.innerText = activity.name;

                        dayDiv.appendChild(activityDiv);
                    } else if (activity.day === day) {
             
                        const height = (endInMinutes - startInMinutes) / 60 * 60; 
                        const top = (startInMinutes % 60) + 'px'; // position from top

                        const activityDiv = document.createElement('div');
                        activityDiv.classList.add('activity');
                        activityDiv.style.height = height + 'px';
                        activityDiv.style.top = top;
                        activityDiv.innerText = activity.name;

                        dayDiv.appendChild(activityDiv);
                    } else if (activity.day !== day) {
                        // For activities that overflow to the next day
                        const nextDay = daysOfWeek[(daysOfWeek.indexOf(day) + 1) % 7];
                        if (nextDay === activity.day) {
                            const height = (endInMinutes - 24 * 60) / 60 * 60; // height in pixels from midnight of next day
                            const top = 0 + 'px'; // position from top at the beginning of next day

                            const activityDiv = document.createElement('div');
                            activityDiv.classList.add('activity');
                            activityDiv.style.height = height + 'px';
                            activityDiv.style.top = top;
                            activityDiv.innerText = activity.name;

                            dayDiv.appendChild(activityDiv);
                        }
                    }
                }
            });

            row.appendChild(dayDiv);
        });

        grid.appendChild(row);
    });
}

// Example: Add an activity
createActivity('Late Meeting', 'Monday', '23:00', '01:00'); // Spans into Tuesday
createActivity('Workout', 'Tuesday', '06:00', '07:00');

// document.getElementById('add-activity').addEventListener('click', function() {
//     const name = document.getElementById('activity-name').value;
//     const day = document.getElementById('activity-day').value;
//     const startTime = document.getElementById('start-time').value;
//     const endTime = document.getElementById('end-time').value;
//     const notes = document.getElementById('notes').value;

//     if (!name || !day || !startTime || !endTime) {
//         alert('Please fill in all fields.');
//         return;
//     }

//     const startHour = parseInt(startTime.split(':')[0]);
//     const startMinute = parseInt(startTime.split(':')[1]);
//     const endHour = parseInt(endTime.split(':')[0]);
//     const endMinute = parseInt(endTime.split(':')[1]);

//     // Calculate the height and position of the activity block
//     const startPosition = (startHour * 60 + startMinute) * (50 / 60); // Convert to pixels
//     const endPosition = (endHour * 60 + endMinute) * (50 / 60); // Convert to pixels
//     const height = endPosition - startPosition;

//     // Create the activity block
//     const activityBlock = document.createElement('div');
//     activityBlock.className = 'activity';
//     activityBlock.style.top = `${startPosition}px`;
//     activityBlock.style.height = `${height}px`;
//     activityBlock.innerText = `${name}\n${notes}`;
//     activityBlock.style.left = `${getDayIndex(day) * 100}px`; // 100px width for each day

//     // Append the activity block to the activities container
//     document.getElementById('activities').appendChild(activityBlock);

//     // Clear the form
//     document.getElementById('activity-name').value = '';
//     document.getElementById('start-time').value = '';
//     document.getElementById('end-time').value = '';
//     document.getElementById('notes').value = '';
// });

// // Function to get the index of the day (0 for Monday, 1 for Tuesday, etc.)
// function getDayIndex(day) {
//     const days = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"];
//     return days.indexOf(day);
// }