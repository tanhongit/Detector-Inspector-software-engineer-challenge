<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wikipedia Graph Generator</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .fade-in {
            animation: fadeIn 0.5s ease-out;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        .pulse {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen">
    <div class="container mx-auto px-4 py-8 max-w-6xl">
        <!-- Header -->
        <div class="text-center mb-10 fade-in">
            <div class="inline-block mb-4">
                <i class="fas fa-chart-line text-6xl text-indigo-600"></i>
            </div>
            <h1 class="text-5xl font-bold text-gray-800 mb-3">
                Wikipedia Graph Generator
            </h1>
            <p class="text-xl text-gray-600">
                Transform Wikipedia tables into beautiful charts instantly
            </p>
        </div>

        <!-- Alert Messages -->
        @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-lg shadow-md fade-in" role="alert">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-2xl mr-3"></i>
                <div>
                    <p class="font-bold">Success!</p>
                    <p>{{ session('success') }}</p>
                </div>
            </div>
        </div>
        @endif

        @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-lg shadow-md fade-in" role="alert">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle text-2xl mr-3"></i>
                <div>
                    <p class="font-bold">Error!</p>
                    <p>{{ session('error') }}</p>
                </div>
            </div>
        </div>
        @endif

        @if($errors->any())
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-lg shadow-md fade-in" role="alert">
            <div class="flex items-center mb-2">
                <i class="fas fa-exclamation-triangle text-2xl mr-3"></i>
                <p class="font-bold">Please fix the following errors:</p>
            </div>
            <ul class="list-disc list-inside ml-8">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <!-- Input Form -->
        <div class="bg-white rounded-2xl shadow-xl p-8 mb-8 fade-in">
            <form action="{{ route('wikipedia-graph.generate') }}" method="POST" class="space-y-6">
                @csrf
                <div>
                    <label for="url" class="block text-lg font-semibold text-gray-700 mb-3">
                        <i class="fas fa-link mr-2 text-indigo-600"></i>
                        Wikipedia URL
                    </label>
                    <div class="relative">
                        <input
                            type="url"
                            name="url"
                            id="url"
                            value="{{ old('url') }}"
                            placeholder="https://en.wikipedia.org/wiki/List_of_countries_by_population"
                            class="w-full px-4 py-4 border-2 border-gray-300 rounded-lg focus:ring-4 focus:ring-indigo-500 focus:border-indigo-500 transition duration-200 text-lg @error('url') border-red-500 @enderror"
                            required
                        >
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <i class="fas fa-globe text-gray-400 text-xl"></i>
                        </div>
                    </div>
                    <p class="mt-2 text-sm text-gray-500">
                        <i class="fas fa-info-circle mr-1"></i>
                        Enter a Wikipedia URL containing tables with numeric data
                    </p>
                </div>

                <button
                    type="submit"
                    class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-bold py-4 px-6 rounded-lg hover:from-indigo-700 hover:to-purple-700 transition duration-300 transform hover:scale-105 shadow-lg text-lg"
                >
                    <i class="fas fa-chart-bar mr-2"></i>
                    Generate Graph
                </button>
            </form>

            <!-- Example URLs -->
            <div class="mt-6 pt-6 border-t border-gray-200">
                <p class="text-sm font-semibold text-gray-700 mb-3">
                    <i class="fas fa-lightbulb mr-2 text-yellow-500"></i>
                    Try these examples:
                </p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                    <button onclick="document.getElementById('url').value='https://en.wikipedia.org/wiki/List_of_countries_by_population'" class="text-left text-sm text-indigo-600 hover:text-indigo-800 hover:bg-indigo-50 p-2 rounded transition">
                        <i class="fas fa-users mr-1"></i> Countries by Population
                    </button>
                    <button onclick="document.getElementById('url').value='https://en.wikipedia.org/wiki/List_of_countries_by_GDP_(nominal)'" class="text-left text-sm text-indigo-600 hover:text-indigo-800 hover:bg-indigo-50 p-2 rounded transition">
                        <i class="fas fa-dollar-sign mr-1"></i> Countries by GDP
                    </button>
                </div>
            </div>
        </div>

        <!-- Generated Graph -->
        @if(session('graph'))
        <div class="bg-white rounded-2xl shadow-xl p-8 mb-8 fade-in">
            <h2 class="text-3xl font-bold text-gray-800 mb-6 flex items-center">
                <i class="fas fa-image mr-3 text-indigo-600"></i>
                Generated Graph
            </h2>
            
            <div class="bg-gray-50 rounded-xl p-6 mb-6">
                <img 
                    src="{{ session('graph')['url'] }}" 
                    alt="Generated Graph" 
                    class="w-full rounded-lg shadow-lg border-4 border-white"
                >
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg p-4">
                    <div class="flex items-center mb-2">
                        <i class="fas fa-table text-blue-600 text-xl mr-2"></i>
                        <span class="font-semibold text-gray-700">Table Size</span>
                    </div>
                    <p class="text-2xl font-bold text-blue-700">
                        {{ session('graph')['table_info']['rows'] }} × {{ session('graph')['table_info']['columns'] }}
                    </p>
                </div>

                <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg p-4">
                    <div class="flex items-center mb-2">
                        <i class="fas fa-hashtag text-green-600 text-xl mr-2"></i>
                        <span class="font-semibold text-gray-700">Numeric Columns</span>
                    </div>
                    <p class="text-2xl font-bold text-green-700">
                        {{ count(session('graph')['columns']) }}
                    </p>
                </div>

                <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg p-4">
                    <div class="flex items-center mb-2">
                        <i class="fas fa-download text-purple-600 text-xl mr-2"></i>
                        <span class="font-semibold text-gray-700">Download</span>
                    </div>
                    <a 
                        href="{{ session('graph')['url'] }}" 
                        download 
                        class="inline-flex items-center text-purple-700 hover:text-purple-900 font-semibold text-lg"
                    >
                        <i class="fas fa-file-image mr-2"></i>
                        PNG Image
                    </a>
                </div>
            </div>

            @if(!empty(session('graph')['columns']))
            <div class="mt-6 pt-6 border-t border-gray-200">
                <h3 class="font-semibold text-gray-700 mb-3">
                    <i class="fas fa-list mr-2 text-indigo-600"></i>
                    Detected Columns:
                </h3>
                <div class="flex flex-wrap gap-2">
                    @foreach(session('graph')['columns'] as $column)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800">
                        <i class="fas fa-chart-line mr-1"></i>
                        {{ $column }}
                    </span>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
        @endif

        <!-- All Graphs Gallery -->
        @if(!empty($graphs) && count($graphs) > 0)
        <div class="bg-white rounded-2xl shadow-xl p-8 fade-in">
            <h2 class="text-3xl font-bold text-gray-800 mb-6 flex items-center">
                <i class="fas fa-images mr-3 text-indigo-600"></i>
                Recent Graphs
                <span class="ml-3 text-lg font-normal text-gray-500">({{ count($graphs) }} total)</span>
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($graphs as $graph)
                <div class="group relative bg-gray-50 rounded-xl overflow-hidden shadow-md hover:shadow-2xl transition duration-300 transform hover:scale-105">
                    <div class="aspect-video overflow-hidden">
                        <img 
                            src="{{ $graph['url'] }}" 
                            alt="Graph" 
                            class="w-full h-full object-cover group-hover:scale-110 transition duration-300"
                        >
                    </div>
                    <div class="p-4 bg-white">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500">
                                <i class="fas fa-clock mr-1"></i>
                                {{ date('M d, Y H:i', $graph['created_at']) }}
                            </span>
                            <a 
                                href="{{ $graph['url'] }}" 
                                download 
                                class="text-indigo-600 hover:text-indigo-800 transition"
                            >
                                <i class="fas fa-download text-lg"></i>
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Footer -->
        <div class="text-center mt-10 text-gray-600">
            <p class="flex items-center justify-center">
                <i class="fas fa-heart text-red-500 mx-2"></i>
                Built with Laravel & Wikipedia Data
            </p>
        </div>
    </div>

    <script>
        // Auto-hide alerts after 5 seconds
        setTimeout(() => {
            const alerts = document.querySelectorAll('[role="alert"]');
            alerts.forEach(alert => {
                alert.style.transition = 'opacity 0.5s ease-out';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);
    </script>
</body>
</html>
