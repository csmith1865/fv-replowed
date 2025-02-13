<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @if (is_dir(public_path('farmville/assets/hashed/assetss')))
                        <h2>Assets exist, Go to the Play tab and enjoy</h2>
                    @else
                        <h2>Assets don't exist.</h2>
                        <p>We need to a routine that downloads all necessary files for the game to work.</p>
                        <p>It's up to you to decide when, but you need to do it.</p>
                        <p>Click the button below to start the process and do not close/interrupt the server while it's doing its thing</p>
                        <br>
                        <p>What will be done:</p>
                        <ul>
                            <li>The game's files will be downloaded from the Internet Archive</li>
                            <li>The files will be extracted to the /public/farmville/assets folder</li>
                        </ul>
                        <br>
                        <button id="download-btn" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">{{ __('Download Assets') }}</button>
                        @if (is_dir(public_path('tmp')) && count(glob(public_path('tmp/') . "*")) == 4)
                        <button id="extract-btn" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">{{ __('Extract Assets') }}</button>
                        @endif
                        <div id="progress-container" style="display: none;">
                            <p>Download Progress: <span id="progress">0</span>%</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#download-btn').click(function () {
                $('#progress-container').show();
                $('#progress').text(0);

                $.ajax({
                    url: "{{ route('download.file') }}",
                    type: "POST",
                    data: { _token: "{{ csrf_token() }}" },
                    success: function (response) {
                        // alert(response.message);
                    }
                });

                // Poll progress every 500ms
                // let progressInterval = setInterval(function () {
                //     $.get("{{ route('download.progress') }}", function (data) {
                //         $('#progress').text("File "+ data.file_num+" Progress "+ data.progress);
                //         if (data.progress >= 100) clearInterval(progressInterval);
                //     });
                // }, 500);
            });

            $('#extract-btn').click(function(){
                console.log("Extract clicked")
                $('#progress-container').show();
                $('#progress').text(0);
                $.ajax({
                    url: "{{ route('extract.file') }}",
                    type: "POST",
                    data: { _token: "{{ csrf_token() }}" },
                    success: function (response) {
                        console.log(response.files)
                    }
                });

                 // Poll progress every 500ms
                 let progressInterval = setInterval(function () {
                    $.get("{{ route('extract.progress') }}", function (data) {
                        $('#progress').text("File "+ data.file_num+" Progress "+ data.progress);
                        if (data.finished == 1) clearInterval(progressInterval);
                    });
                }, 500);
            })
        });
    </script>
</x-app-layout>