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
        }
        .modal-content {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 90%;
            max-width: 800px; /* Wider for the two-column layout */
            max-height: 90vh;
            overflow-y: auto;
            background-color: #1f2937; /* Lighter dark */
            color: #d1d5db;
            border-radius: 0.75rem;
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.3);
            z-index: 101;
        }
        
        /* --- NEW: Two-column layout for the modal body --- */
        #movieModalBody {
            display: grid;
            grid-template-columns: 1fr; /* Default for mobile */
            gap: 1.5rem;
        }
        
        @media (min-width: 640px) { /* sm: breakpoint */
             #movieModalBody {
                grid-template-columns: 250px 1fr; /* 2-column layout on desktop */
            }
        }

        #modal-poster {
            width: 100%;
            height: auto;
            border-radius: 0.5rem;
            background-color: #374151; /* Placeholder bg */
        }
        
        /* --- NEW: Style for our TMDb Link --- */
        .tmdb-link {
            display: inline-block;
            margin-top: 0.75rem;
            padding: 0.5rem 1rem;
            background-color: #0d253f; /* TMDb Dark Blue */
            color: #90cea1; /* TMDb Green */
            font-weight: bold;
            text-decoration: none;
            border-radius: 0.5rem;
            transition: all 0.2s ease;
        }
        .tmdb-link:hover {
            background-color: #01b4e4; /* TMDb Light Blue */
            color: #0d253f;
        }

        /* Pulse animation for unlocked days */
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: .8; }
        }
        .day-box-unlocked:not(.day-box-watched) {
            animation: pulse 2.5s infinite;
        }

    </style>
</head>
<body class="bg-gray-900 text-gray-300">

    <!-- Main Container -->
    <div class="container mx-auto p-4 md:p-8 max-w-4xl min-h-screen">
        
        <!-- Header -->
        <header class="text-center mb-8">
            <h1 class="font-christmas text-6xl md:text-8xl text-red-600 mb-2">Movie Marathon</h1>
            <h2 id="countdown" class="text-xl md:text-3xl text-gray-300 mb-2">Only 00 Days 'til Christmas!</h2>
            <p class="text-md md:text-lg text-gray-400">Curated by Chris, Emma, & Inga</p>
        </header>

        <!-- Movie Grid -->
        <main id="movie-grid" class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-3 md:gap-5">
            <!-- Day boxes will be generated by JavaScript -->
        </main>
        
        <!-- Footer -->
        <footer class="text-center text-gray-500 text-sm mt-12">
            <p>Designed with ❤️ by Chris and Emma.</p>
            <p>This product uses the TMDb API but is not endorsed or certified by TMDb.</p>
        </footer>

    </div>

    <!-- Modal Container -->
    <div id="movieModal" class="modal-overlay hidden" aria-hidden="true">
        <div class="modal-content" role="dialog" aria-modal="true" aria-labelledby="modal-title">
            
            <!-- Modal Header -->
            <header class="flex justify-between items-center p-4 border-b border-gray-600">
                <h3 id="modal-title" class="text-2xl font-bold text-white">Loading...</h3>
                <button id="closeModal" class="text-gray-400 hover:text-white" aria-label="Close modal">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </header>
            
            <!-- 
              =======================================================
              Modal Body (RE-IMPLEMENTED TWO-COLUMN LAYOUT)
              =======================================================
            -->
            <div id="movieModalBody" class="p-6">
                
                <!-- Left Column (Poster) -->
                <div>
                    <img id="modal-poster" 
                         src="" 
                         alt="Movie Poster" 
                         class="w-full h-auto rounded-lg bg-gray-700">
                </div>
                
                <!-- Right Column (Info) -->
                <div>
                    <div class="mb-3">
                        <span class="text-sm font-bold text-gray-400">Picked by:</span>
                        <p id="modal-picked-by" class="text-lg text-white">Loading...</p>
                    </div>
                    
                    <div class="mb-3">
                        <span class="text-sm font-bold text-gray-400">Rating:</span>
                        <p id="modal-rating" class="text-lg text-white">N/A</p>
                    </div>

                    <div class="mb-3">
                        <span class="text-sm font-bold text-gray-400">Description:</span>
                        <p id="modal-description" class="text-sm text-gray-300">Loading description...</p>
                    </div>

                    <div class="mb-3">
                        <span class="text-sm font-bold text-gray-400">Where to Watch:</span>
                        <p id="modal-streaming" class="text-lg text-white">Loading...</p>
                        <p class="text-xs text-gray-500">(Streaming info manually updated and may change.)</p>
                        
                        <a id="modal-tmdb-link" 
                           href="#" 
                           target="_blank" 
                           rel="noopener noreferrer" 
                           class="tmdb-link">
                           See on TMDb (for streaming info) &rarr;
                        </a>
                    </div>

                    <!-- "Watched It" Checkbox -->
                    <div class="mt-4 bg-gray-800 p-3 rounded-lg">
                        <label for="modal-watched" class="flex items-center cursor-pointer">
                            <input type="checkbox" id="modal-watched" class="h-5 w-5 rounded bg-gray-700 border-gray-600 text-red-600 focus:ring-red-500">
                            <span class="ml-3 text-lg text-white">I've watched this!</span>
                        </label>
                    </div>
                </div>

            </div>

        </div>
    </div>

    <script>
        // --- Snow Effect ---
        // (This code is unchanged and works)
        (function() {
            function generateSnow() {
                const sheet = document.getElementById('snow-styles').sheet;
                if (!sheet) {
                    console.error("Snow stylesheet not found.");
                    return;
                }
                const snowflake = '❄️';
                const count = 50;
                let keyframes = '@keyframes fall { 0% { opacity: 1; transform: translate(0, -10vh); } 100% { opacity: 1; transform: translate(var(--x-end), 110vh); } }';
                try {
                    sheet.insertRule(keyframes, sheet.cssRules.length);
                } catch (e) {
                    console.error("Failed to insert @keyframes rule:", e);
                    return; 
                }

                for (let i = 0; i < count; i++) {
                    const xStart = Math.random() * 100;
                    const xEnd = Math.random() * 100;
                    const duration = (Math.random() * 10) + 5;
                    const delay = Math.random() * -15;
                    const size = (Math.random() * 0.8) + 0.2;

                    let rule = `
                        .snow:nth-child(${i + 1}) {
                            --x-end: ${xEnd}vw;
                            left: ${xStart}vw;
                            animation: fall ${duration}s ${delay}s linear infinite;
                            font-size: ${size}em;
                        }
                    `;
                    try {
                        sheet.insertRule(rule, sheet.cssRules.length);
                    } catch (e) {
                        console.error("Failed to insert snow rule:", e);
                    }
                }

                const snowContainer = document.createElement('div');
                snowContainer.className = 'snow-container';
                snowContainer.setAttribute('aria-hidden', 'true');
                for (let i = 0; i < count; i++) {
                    const span = document.createElement('span');
                    span.className = 'snow';
                    span.textContent = snowflake;
                    snowContainer.appendChild(span);
                }
                document.body.appendChild(snowContainer);
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', generateSnow);
            } else {
                generateSnow();
            }
        })();

        // --- App Logic ---
        document.addEventListener('DOMContentLoaded', () => {
            
            // ---
            // --- IMPORTANT! ---
            // Put your TMDb API Key here, partner!
            // ---
            const API_KEY = '029deb812d6a02185b3e9c54cbc3b68e'; 
            // ---
            // ---

            const API_BASE_URL = 'https://api.themoviedb.org/3/movie/';
            const IMAGE_BASE_URL = 'https://image.tmdb.org/t/p/w400';
            const PLACEHOLDER_POSTER_URL = 'https://placehold.co/400x600/1f2937/374151?text=No+Poster';

            const grid = document.getElementById('movie-grid');
            const modal = document.getElementById('movieModal');
            const closeModalBtn = document.getElementById('closeModal');
            const watchedCheckbox = document.getElementById('modal-watched');
            
            let currentDayId = null; // e.g., "movie-1"
            let movieDataCache = []; // Caches our PHP fetch

            // --- 1. Initialize the App ---
            initializeApp();

            async function initializeApp() {
                updateCountdown();
                await fetchMovieData();
                generateGrid();
            }

            // --- 2. Update Countdown ---
            function updateCountdown() {
                const today = new Date();
                const christmas = new Date(today.getFullYear(), 11, 25);
                if (today.getMonth() == 11 && today.getDate() > 25) {
                    christmas.setFullYear(christmas.getFullYear() + 1);
                }
                const oneDay = 1000 * 60 * 60 * 24;
                const daysLeft = Math.ceil((christmas.getTime() - today.getTime()) / oneDay);
                document.getElementById('countdown').textContent = `Only ${daysLeft} Days 'til Christmas!`;
            }

            // --- 3. Fetch Movie Data from Our PHP Script ---
            async function fetchMovieData() {
                try {
                    const response = await fetch('get_movies.php');
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    movieDataCache = await response.json();
                } catch (error) {
                    console.error("Failed to fetch movie list:", error);
                    grid.innerHTML = `<p class="text-red-400 col-span-full">Error: Could not load movie data. Is the PHP file running?</p>`;
                }
            }

            // --- 4. Generate the 25-Day Grid ---
            function generateGrid() {
                grid.innerHTML = ''; // Clear grid
                const watchedMovies = getWatchedMovies();
                
                for (let i = 1; i <= 25; i++) {
                    const dayBox = document.createElement('div');
                    dayBox.className = 'day-box flex items-center justify-center rounded-lg';
                    
                    const movie = movieDataCache.find(m => m.day == i);
                    
                    if (movie) {
                        // This day is UNLOCKED
                        dayBox.classList.add('day-box-unlocked');
                        dayBox.innerHTML = `<span class="number">${i}</span>`;
                        dayBox.dataset.movieId = i;
                        dayBox.setAttribute('role', 'button');
                        dayBox.setAttribute('tabindex', '0');
                        dayBox.setAttribute('aria-label', `View movie for Day ${i}: ${movie.movie_title}`);
                        
                        // Check if it's been watched
                        if (watchedMovies.includes(`movie-${i}`)) {
                            dayBox.classList.add('day-box-watched');
                        }
                        
                        dayBox.addEventListener('click', () => openModal(movie));
                        dayBox.addEventListener('keydown', (e) => {
                            if (e.key === 'Enter' || e.key === ' ') openModal(movie);
                        });
                    } else {
                        // This day is LOCKED
                        dayBox.classList.add('day-box-locked');
                        dayBox.innerHTML = `<svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>`;
                    }
                    grid.appendChild(dayBox);
                }
            }
            
            // --- 5. Open and Populate the Modal ---
            async function openModal(movie) {
                currentDayId = `movie-${movie.day}`; // e.g., "movie-1"
                
                // --- Step 5a: Populate with OUR database data (instant) ---
                document.getElementById('modal-title').textContent = movie.movie_title;
                document.getElementById('modal-picked-by').textContent = movie.picked_by || 'N/A';
                document.getElementById('modal-streaming').textContent = movie.streaming_notes || 'Check TMDb';
                
                // Set the dynamic TMDb link URL
                const tmdbLink = document.getElementById('modal-tmdb-link');
                if (movie.tmdb_id) {
                    tmdbLink.href = `https://www.themoviedb.org/movie/${movie.tmdb_id}`;
                    tmdbLink.style.display = 'inline-block';
                } else {
                    tmdbLink.style.display = 'none';
                }
                
                // Set modal to default/loading state before API call
                document.getElementById('modal-poster').src = PLACEHOLDER_POSTER_URL;
                document.getElementById('modal-description').textContent = 'Loading description...';
                document.getElementById('modal-rating').textContent = 'N/A';

                // Check "Watched" status
                watchedCheckbox.checked = getWatchedMovies().includes(currentDayId);
                
                modal.classList.remove('hidden');
                modal.setAttribute('aria-hidden', 'false');
                document.getElementById('modal-title').focus(); // Focus the modal title for accessibility

                // --- Step 5b: Fetch data from TMDb (the "ambitious" part) ---
                if (!movie.tmdb_id) {
                    document.getElementById('modal-description').textContent = 'No TMDb ID found for this movie.';
                    return; // Don't even try to fetch
                }
                
                if (!API_KEY || API_KEY === 'YOUR_TMDB_API_KEY_GOES_HERE') {
                    console.error("TMDb API Key is missing!");
                    document.getElementById('modal-description').textContent = 'TMDb API Key is missing. Cannot load details.';
                    return;
                }

                try {
                    const response = await fetch(`${API_BASE_URL}${movie.tmdb_id}?api_key=${API_KEY}`);
                    if (!response.ok) {
                        throw new Error(`API returned ${response.status}`);
                    }
                    const data = await response.json();

                    // --- Step 5c: Populate with TMDb data (Success!) ---
                    
                    // Set Poster (with fallback)
                    if (data.poster_path) {
                        document.getElementById('modal-poster').src = `${IMAGE_BASE_URL}${data.poster_path}`;
                    } else {
                        document.getElementById('modal-poster').src = PLACEHOLDER_POSTER_URL;
                    }
                    
                    // Set Description (with fallback)
                    if (data.overview) {
                        document.getElementById('modal-description').textContent = data.overview;
                    } else {
                        document.getElementById('modal-description').textContent = 'No description available from TMDb.';
                    }
                    
                    // Set Rating (with fallback)
                    if (data.vote_average && data.vote_average > 0) {
                        document.getElementById('modal-rating').textContent = `${data.vote_average.toFixed(1)} / 10`;
                    } else {
                        document.getElementById('modal-rating').textContent = 'N/A';
                    }

                } catch (error) {
                    // --- Step 5d: Handle API Error (Fallback) ---
                    console.error("Error fetching TMDb data:", error);
                    document.getElementById('modal-poster').src = PLACEHOLDER_POSTER_URL;
                    document.getElementById('modal-description').textContent = 'Could not load movie details. Please use the TMDb link for more info.';
                    document.getElementById('modal-rating').textContent = 'N/A';
                }
            }

            // --- 6. Close the Modal ---
            function closeModal() {
                modal.classList.add('hidden');
                modal.setAttribute('aria-hidden', 'true');
                currentDayId = null;
            }
            
            // --- 7. Handle "Watched It" Checkbox ---
            function getWatchedMovies() {
                return JSON.parse(localStorage.getItem('watchedHolidayMovies') || '[]');
            }
            
            function handleWatchToggle() {
                if (!currentDayId) return;
                
                const watchedMovies = getWatchedMovies();
                const dayBox = document.querySelector(`.day-box[data-movie-id="${currentDayId.split('-')[1]}"]`);

                if (watchedCheckbox.checked) {
                    // Add to list
                    if (!watchedMovies.includes(currentDayId)) {
                        watchedMovies.push(currentDayId);
                    }
                    if (dayBox) dayBox.classList.add('day-box-watched');
                } else {
                    // Remove from list
                    const index = watchedMovies.indexOf(currentDayId);
                    if (index > -1) {
                        watchedMovies.splice(index, 1);
                    }
                    if (dayBox) dayBox.classList.remove('day-box-watched');
                }
                
                localStorage.setItem('watchedHolidayMovies', JSON.stringify(watchedMovies));
            }

            // --- Event Listeners ---
            closeModalBtn.addEventListener('click', closeModal);
            modal.addEventListener('click', (e) => {
                if (e.target === modal) closeModal(); // Click outside modal content
            });
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
                    closeModal();
                }
            });
            watchedCheckbox.addEventListener('change', handleWatchToggle);

        });
    </script>

</body>
</html>

