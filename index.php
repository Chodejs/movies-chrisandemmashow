<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Holiday Movie Marathon</title>
    
    <!-- Open Graph Tags (from your brilliant idea!) -->
    <meta property="og:title" content="Chris & Emma's Holiday Movie Marathon">
    <meta property="og:description" content="Join our 25-day Advent calendar of festive (and not-so-festive) movie picks. See what we picked and track your progress!">
    <meta property="og:image" content="https://placehold.co/1200x630/c53030/ffffff?text=Holiday+Movie+Marathon&font=inter">
    <meta property="og:url" content="https://movies.chrisandemmashow.com">
    <meta property="og:type" content="website">
    <meta name="twitter:card" content="summary_large_image">

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&family=Mountains+of+Christmas:wght@700&display=swap" rel="stylesheet">
    
    <!-- 
      This is our dedicated <style> tag for the snow.
      This fixed the "insertRule" error from before by giving the
      snow animation its own non-Tailwind stylesheet to write to.
    -->
    <style id="snow-styles"></style>

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #111827; /* Dark blue-gray */
            color: #d1d5db;
        }
        .font-christmas {
            font-family: 'Mountains of Christmas', cursive;
        }
        
        /* --- Day Box Styles --- */
        .day-box {
            aspect-ratio: 1 / 1;
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }
        .day-box .number {
            font-size: 2.5rem;
            line-height: 1;
            font-weight: 700;
            transition: all 0.3s ease;
        }
        
        /* Locked State (Default) */
        .day-box-locked {
            background-color: #374151; /* Medium gray */
            cursor: not-allowed;
            color: #9ca3af;
        }
        .day-box-locked .number {
            opacity: 0.5;
        }
        
        /* Unlocked State */
        .day-box-unlocked {
            background-color: #c53030; /* Festive red */
            color: white;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.4), inset 0 2px 4px rgba(255, 255, 255, 0.2);
        }
        .day-box-unlocked:hover {
            transform: scale(1.05);
            background-color: #e53e3e;
            z-index: 10;
        }
        .day-box-unlocked:hover .number {
            transform: scale(1.1);
        }
        
        /* Watched State (Set by JS) */
        .day-box-watched {
            background-color: #166534; /* Festive green */
            color: #d1d5db;
        }
        .day-box-watched:hover {
            background-color: #15803d;
        }

        /* --- Modal Styles --- */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(5px);
            z-index: 99;
            display: none; /* Hidden by default */
        }
        .modal-content {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 95%; /* Mobile-first: almost full width */
            max-width: 500px; /* Mobile-first max-width */
            max-height: 90vh;
            overflow-y: auto;
            background-color: #1f2937; /* Lighter dark */
            color: #d1d5db;
            border-radius: 0.75rem;
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.3);
            z-index: 101;
        }
        
        /* --- Two-column layout for the modal body --- */
        #movieModalBody {
            display: grid;
            grid-template-columns: 1fr; /* Mobile-first: stacked by default */
            gap: 1.5rem;
        }

        /* --- Grid Overflow Fix --- */
        /* This tiny rule tells the grid's children that it's okay 
           to shrink smaller than their content, fixing the overflow. */
        #movieModalBody > div {
            min-width: 0;
        }
        
        /* --- Media Query for Desktop --- */
        /* This tells the modal to switch to 2 columns on screens 768px wide or larger */
        @media (min-width: 768px) {
             /* 1. Widen the modal itself on desktop */
            .modal-content {
                max-width: 800px;
            }
             /* 2. Apply the 2-column grid layout on desktop */
             #movieModalBody {
                grid-template-columns: 250px 1fr;
            }
        }

        #modal-poster {
            width: 100%;
            height: auto;
            border-radius: 0.5rem;
            background-color: #374151;
            display: none; /* Hide until loaded */
        }

        #modal-poster-placeholder {
            width: 100%;
            aspect-ratio: 2 / 3;
            background-color: #374151;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: #9ca3af;
        }

        /* --- Snow Styles --- */
        .snow-flake {
            position: fixed;
            top: -10px;
            color: #fff;
            user-select: none;
            pointer-events: none;
            z-index: 98;
            font-size: 1.5rem;
            opacity: 0.7;
        }

    </style>
</head>
<body class="min-h-screen">

    <!-- Main Content Wrapper -->
    <div class="container mx-auto px-4 py-8 max-w-5xl">

        <!-- Header -->
        <header class="text-center mb-8">
            <h1 class="font-christmas text-6xl md:text-8xl text-red-600 mb-2">Holiday Movie Marathon</h1>
            <p id="countdown" class="text-xl md:text-2xl font-bold text-gray-300">Only 64 Days 'til Christmas!</p>
            <p class="text-lg text-gray-400">Curated by Chris, Emma, & Inga</p>
        </header>

        <!-- Advent Calendar Grid -->
        <main id="calendarGrid" class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-3 md:gap-4">
            <!-- Days 1-25 will be injected here by JavaScript -->
        </main>
        
        <!-- Footer -->
        <footer class="text-center mt-12 text-gray-500">
            <p>Designed with ❤️ by Chris and Emma.</p>
        </footer>

    </div>

    <!-- Modal Overlay and Content -->
    <div id="movieModal" class="modal-overlay" onclick="closeModal()">
        <div class="modal-content" onclick="event.stopPropagation()">
            
            <!-- Modal Header -->
            <div class="flex justify-between items-center p-4 sm:p-6 border-b border-gray-600">
                <h2 id="modalTitle" class="text-2xl font-bold text-white">Loading...</h2>
                <button onclick="closeModal()" class="text-gray-400 hover:text-white text-3xl">&times;</button>
            </div>

            <!-- Modal Body (Our 2-Column Grid) -->
            <div id="movieModalBody" class="p-4 sm:p-6">
                
                <!-- Column 1: Poster -->
                <div id="modal-poster-container">
                    <!-- The real poster (hidden by default) -->
                    <img id="modal-poster" src="" alt="Movie Poster">
                    <!-- The placeholder (shown by default) -->
                    <div id="modal-poster-placeholder">
                        <span>No Poster</span>
                    </div>
                </div>
                
                <!-- Column 2: Details -->
                <div id="modal-details" class="flex flex-col">
                    <!-- Picked By & Rating -->
                    <div class="mb-4">
                        <span class="text-sm font-semibold bg-gray-600 text-gray-200 px-3 py-1 rounded-full" id="modalPickedBy">
                            Picked by: ...
                        </span>
                        <span class_ ="text-sm font-semibold text-gray-300 ml-3" id="modalRating">
                            Rating: N/A
                        </span>
                    </div>

                    <!-- Description -->
                    <p class="text-gray-300 mb-4" id="modalDescription">
                        Loading description...
                    </p>
                    
                    <!-- Streaming Info -->
                    <div class="mt-auto pt-4 border-t border-gray-700">
                        <p class="font-bold text-white mb-1">Where to Watch:</p>
                        <p class="text-gray-300 mb-3" id="modalStreaming">
                            Loading...
                        </p>

                        <!-- TMDb Link (Our new brilliant idea!) -->
                        <a id="modalTMDbLink" href="#" target="_blank" class="inline-block text-blue-400 hover:text-blue-300 hover:underline transition-colors duration-200">
                            See on TMDb
                        </a>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="flex flex-col sm:flex-row justify-between items-center p-4 sm:p-6 bg-gray-800 rounded-b-lg">
                <!-- "Watched It" Checkbox -->
                <label for="watchedCheck" class="flex items-center space-x-2 text-lg text-gray-200 cursor-pointer mb-4 sm:mb-0">
                    <input type="checkbox" id="watchedCheck" class="h-5 w-5 rounded text-red-600 bg-gray-700 border-gray-600 focus:ring-red-500">
                    <span>I've watched this!</span>
                </label>

                <!-- Trailer Button -->
                <a href="#" id="modalTrailerButton" target="_blank" class="w-full sm:w-auto inline-block text-center bg-red-600 text-white font-bold py-2 px-6 rounded-lg hover:bg-red-700 transition-colors duration-200">
                    Watch Trailer
                </a>
            </div>
            
        </div>
    </div>


    <script>
        // ---
        // CONFIGURATION
        // ---
        // This is the PHP file that securely connects to our database.
        const API_ENDPOINT = 'get_movies.php'; 
        
        // ---
        // IMPORTANT! API KEY IS NOW INSERTED!
        // ---
        const API_KEY = '029deb812d6a02185b3e9c54cbc3b68e'; 
        // ---

        // This object will hold our movie data once we fetch it.
        let movieDataStore = {};
        
        // This object will store which movies the user has watched.
        let watchedList = {};

        // ---
        // INITIALIZATION
        // ---

        // This is the main function that kicks everything off.
        document.addEventListener('DOMContentLoaded', () => {
            initializeApp();
        });

        async function initializeApp() {
            try {
                // 1. Start the snow
                generateSnow();
                
                // 2. Start the countdown timer
                startCountdown();
                
                // 3. Load the user's watched list from their browser's localStorage
                loadWatchedList();

                // 4. Fetch the movie list from our database (via the PHP script)
                const movies = await fetchMovies();
                
                // 5. Store the fetched movies for later
                movies.forEach(movie => {
                    movieDataStore[movie.day] = movie;
                });
                
                // 6. Build the 25-day grid
                buildCalendarGrid();

            } catch (error) {
                console.error("Error initializing app:", error);
                // We could show a user-facing error here if we wanted
            }
        }

        // ---
        // CORE LOGIC
        // ---

        /**
         * Fetches the unlocked movie list from our PHP backend.
         */
        async function fetchMovies() {
            try {
                const response = await fetch(API_ENDPOINT);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                const data = await response.json();
                
                // Handle database connection errors from the PHP script
                if (data.error) {
                    console.error("Database Error:", data.error);
                    throw new Error(data.error);
                }
                
                return data;
            } catch (error) {
                console.error("Failed to fetch movie list:", error);
                // This is where the user would see "Failed to fetch" if the PHP file is broken.
                const grid = document.getElementById('calendarGrid');
                grid.innerHTML = `<p class="text-red-400 col-span-full text-center">Failed to load movie data. Please check the 'get_movies.php' file and database connection.</p>`;
                return []; // Return an empty array to prevent further errors
            }
        }

        /**
         * Creates the 25 boxes for the calendar.
         */
        function buildCalendarGrid() {
            const grid = document.getElementById('calendarGrid');
            grid.innerHTML = ''; // Clear it out first

            for (let i = 1; i <= 25; i++) {
                const day = i;
                const movie = movieDataStore[day];
                
                const box = document.createElement('div');
                box.className = 'day-box flex items-center justify-center rounded-lg';
                
                if (movie) {
                    // This day is UNLOCKED
                    box.classList.add('day-box-unlocked');
                    
                    // Check if it's been watched
                    if (watchedList[day]) {
                        box.classList.add('day-box-watched');
                    }
                    
                    box.onclick = () => openModal(day);
                    
                    // Check for watched status to show icon
                    if (watchedList[day]) {
                        box.innerHTML = `<span class="number">✔</span>`;
                    } else {
                        box.innerHTML = `<span class="number">${day}</span>`;
                    }
                    
                } else {
                    // This day is LOCKED
                    box.classList.add('day-box-locked');
                    box.innerHTML = `
                        <span class="number">${day}</span>
                        <svg class="w-8 h-8 absolute" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>`;
                }
                grid.appendChild(box);
            }
        }

        /**
         * Opens the modal and fetches TMDb data.
         */
        async function openModal(day) {
            const movie = movieDataStore[day];
            if (!movie) return;

            // --- 1. Reset Modal to "Loading" State ---
            // This is crucial for our new "fallback" logic.
            
            // Show modal
            document.getElementById('movieModal').style.display = 'block';
            
            // Set our internal database info first (this is instant)
            document.getElementById('modalTitle').textContent = movie.movie_title;
            document.getElementById('modalPickedBy').textContent = `Picked by: ${movie.picked_by || 'Us'}`;
            document.getElementById('modalStreaming').textContent = movie.streaming_notes || 'Check TMDb';

            // Set the TMDb link dynamically (our great new idea)
            const tmdbLink = document.getElementById('modalTMDbLink');
            if (movie.tmdb_id) {
                tmdbLink.href = `https://www.themoviedb.org/movie/${movie.tmdb_id}`;
                tmdbLink.style.display = 'inline-block';
            } else {
                tmdbLink.style.display = 'none'; // Hide if no ID
            }

            // Set default/placeholder states
            document.getElementById('modalRating').textContent = 'Rating: N/A';
            document.getElementById('modalDescription').textContent = 'Loading description...';
            document.getElementById('modalTrailerButton').style.display = 'none'; // Hide by default
            
            // Show placeholder, hide real poster
            document.getElementById('modal-poster').style.display = 'none';
            document.getElementById('modal-poster-placeholder').style.display = 'flex';
            
            // Set checkbox state
            const watchedCheck = document.getElementById('watchedCheck');
            watchedCheck.checked = !!watchedList[day]; // Set checked status
            watchedCheck.onchange = () => toggleWatchedStatus(day); // Set change listener

            // --- 2. Check for API Key ---
            if (!API_KEY || API_KEY === 'YOUR_TMDB_API_KEY_GOES_HERE') {
                document.getElementById('modalDescription').textContent = 'TMDb API Key is missing. Cannot load details.';
                document.getElementById('modal-poster-placeholder').innerHTML = '<span>API Key Missing</span>';
                return; // Stop here
            }

            // --- 3. Fetch Data from TMDb (The "Enhancement") ---
            try {
                // Fetch main movie details
                const response = await fetch(`https://api.themoviedb.org/3/movie/${movie.tmdb_id}?api_key=${API_KEY}&append_to_response=videos`);
                if (!response.ok) {
                    throw new Error(`TMDb API error! Status: ${response.status}`);
                }
                const data = await response.json();

                // --- 4. Populate Modal with Rich Data ---
                
                // Set Poster
                if (data.poster_path) {
                    const posterImg = document.getElementById('modal-poster');
                    posterImg.src = `https://image.tmdb.org/t/p/w500${data.poster_path}`;
                    posterImg.style.display = 'block'; // Show real poster
                    document.getElementById('modal-poster-placeholder').style.display = 'none'; // Hide placeholder
                } else {
                    // No poster? No problem. We just keep the placeholder.
                    document.getElementById('modal-poster-placeholder').innerHTML = '<span>No Poster</span>';
                }

                // Set Rating
                if (data.vote_average && data.vote_average > 0) {
                    document.getElementById('modalRating').textContent = `Rating: ${data.vote_average.toFixed(1)}/10`;
                } else {
                    document.getElementById('modalRating').textContent = 'Rating: N/A';
                }

                // Set Description
                document.getElementById('modalDescription').textContent = data.overview || 'No description available.';

                // Find Trailer
                const trailer = data.videos?.results.find(video => video.type === 'Trailer' && video.site === 'YouTube');
                if (trailer) {
                    const trailerButton = document.getElementById('modalTrailerButton');
                    trailerButton.href = `https://www.youtube.com/watch?v=${trailer.key}`;
                    trailerButton.style.display = 'inline-block'; // Show the button
                }

            } catch (error) {
                console.error("Failed to fetch from TMDb:", error);
                // If it fails, we just show the placeholders, which is our new robust plan!
                document.getElementById('modalDescription').textContent = 'Could not load TMDb details. Check the link below.';
                document.getElementById('modal-poster-placeholder').innerHTML = '<span>No Poster</span>';
            }
        }

        /**
         * Closes the modal.
         */
        function closeModal() {
            document.getElementById('movieModal').style.display = 'none';
        }
        
        // ---
        // HELPER FUNCTIONS
        // ---

        /**
         * Loads the user's watched list from localStorage.
         */
        function loadWatchedList() {
            const storedList = localStorage.getItem('holidayMovieWatchedList');
            if (storedList) {
                watchedList = JSON.parse(storedList);
            }
        }

        /**
         * Saves the user's watched list to localStorage.
         */
        function saveWatchedList() {
            localStorage.setItem('holidayMovieWatchedList', JSON.stringify(watchedList));
        }

        /**
         * Toggles the "watched" status for a day.
         */
        function toggleWatchedStatus(day) {
            if (watchedList[day]) {
                delete watchedList[day];
            } else {
                watchedList[day] = true;
            }
            saveWatchedList(); // Save the new list
            buildCalendarGrid(); // Re-draw the grid to show the checkmark
        }

        /**
         * Starts the Christmas countdown timer.
         */
        function startCountdown() {
            const countdownEl = document.getElementById('countdown');
            if (!countdownEl) return;

            const christmas = new Date(new Date().getFullYear(), 11, 25);
            
            function update() {
                const now = new Date();
                
                // Check if it's already past Christmas
                if (now > christmas) {
                    countdownEl.textContent = 'Merry Christmas!';
                    return;
                }
                
                const diff = christmas.getTime() - now.getTime();
                const days = Math.ceil(diff / (1000 * 60 * 60 * 24));
                
                countdownEl.textContent = `Only ${days} Day${days > 1 ? 's' : ''} 'til Christmas!`;
            }
            
            update(); // Run once immediately
            setInterval(update, 60000); // And then update every minute
        }

        /**
         * Generates the falling snow effect.
         */
        function generateSnow() {
            const styleSheet = document.getElementById('snow-styles').sheet;
            const flakes = 50; // Number of flakes

            for (let i = 0; i < flakes; i++) {
                const size = (Math.random() * 0.5 + 0.2) + 'rem'; // 0.2rem to 0.7rem
                const duration = (Math.random() * 5 + 5) + 's'; // 5s to 10s
                const delay = (Math.random() * -10) + 's'; // Start at different times
                const xStart = (Math.random() * 100) + 'vw';
                const xEnd = (Math.random() * 100) + 'vw';

                // Keyframe animation
                const keyframeName = `fall-${i}`;
                styleSheet.insertRule(`
                    @keyframes ${keyframeName} {
                        from { transform: translate3d(${xStart}, -10px, 0); }
                        to { transform: translate3d(${xEnd}, 105vh, 0); }
                    }
                `, styleSheet.cssRules.length);

                // Flake element
                const flake = document.createElement('div');
                flake.className = 'snow-flake';
                flake.textContent = '•'; // Simple dot, less "cheesy" than *
                flake.style.left = (Math.random() * 100) + 'vw';
                flake.style.fontSize = size;
                flake.style.animation = `${keyframeName} ${duration} ${delay} linear infinite`;
                document.body.appendChild(flake);
            }
        }
        
        // Close modal with 'Escape' key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeModal();
            }
        });

    </script>
</body>
</html>

