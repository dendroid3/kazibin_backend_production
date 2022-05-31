<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>kazibin</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Dosis:wght@200;300;400&display=swap" rel="stylesheet">

        <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

        <!-- Styles -->
        <style>
            /*! normalize.css v8.0.1 | MIT License | github.com/necolas/normalize.css */html{line-height:1.15;-webkit-text-size-adjust:100%}body{margin:0}a{background-color:transparent}[hidden]{display:none}html{font-family:system-ui,-apple-system,BlinkMacSystemFont,Segoe UI,Roboto,Helvetica Neue,Arial,Noto Sans,sans-serif,Apple Color Emoji,Segoe UI Emoji,Segoe UI Symbol,Noto Color Emoji;line-height:1.5}*,:after,:before{box-sizing:border-box;border:0 solid #e2e8f0}a{color:inherit;text-decoration:inherit}svg,video{display:block;vertical-align:middle}video{max-width:100%;height:auto}.bg-white{--bg-opacity:1;background-color:#fff;background-color:rgba(255,255,255,var(--bg-opacity))}.bg-gray-100{--bg-opacity:1;background-color:#f7fafc;background-color:rgba(247,250,252,var(--bg-opacity))}.border-gray-200{--border-opacity:1;border-color:#edf2f7;border-color:rgba(237,242,247,var(--border-opacity))}.border-t{border-top-width:1px}.flex{display:flex}.grid{display:grid}.hidden{display:none}.items-center{align-items:center}.justify-center{justify-content:center}.font-semibold{font-weight:600}.h-5{height:1.25rem}.h-8{height:2rem}.h-16{height:4rem}.text-sm{font-size:.875rem}.text-lg{font-size:1.125rem}.leading-7{line-height:1.75rem}.mx-auto{margin-left:auto;margin-right:auto}.ml-1{margin-left:.25rem}.mt-2{margin-top:.5rem}.mr-2{margin-right:.5rem}.ml-2{margin-left:.5rem}.mt-4{margin-top:1rem}.ml-4{margin-left:1rem}.mt-8{margin-top:2rem}.ml-12{margin-left:3rem}.-mt-px{margin-top:-1px}.max-w-6xl{max-width:72rem}.min-h-screen{min-height:100vh}.overflow-hidden{overflow:hidden}.p-6{padding:1.5rem}.py-4{padding-top:1rem;padding-bottom:1rem}.px-6{padding-left:1.5rem;padding-right:1.5rem}.pt-8{padding-top:2rem}.fixed{position:fixed}.relative{position:relative}.top-0{top:0}.right-0{right:0}.shadow{box-shadow:0 1px 3px 0 rgba(0,0,0,.1),0 1px 2px 0 rgba(0,0,0,.06)}.text-center{text-align:center}.text-gray-200{--text-opacity:1;color:#edf2f7;color:rgba(237,242,247,var(--text-opacity))}.text-gray-300{--text-opacity:1;color:#e2e8f0;color:rgba(226,232,240,var(--text-opacity))}.text-gray-400{--text-opacity:1;color:#cbd5e0;color:rgba(203,213,224,var(--text-opacity))}.text-gray-500{--text-opacity:1;color:#a0aec0;color:rgba(160,174,192,var(--text-opacity))}.text-gray-600{--text-opacity:1;color:#718096;color:rgba(113,128,150,var(--text-opacity))}.text-gray-700{--text-opacity:1;color:#4a5568;color:rgba(74,85,104,var(--text-opacity))}.text-gray-900{--text-opacity:1;color:#1a202c;color:rgba(26,32,44,var(--text-opacity))}.underline{text-decoration:underline}.antialiased{-webkit-font-smoothing:antialiased;-moz-osx-font-smoothing:grayscale}.w-5{width:1.25rem}.w-8{width:2rem}.w-auto{width:auto}.grid-cols-1{grid-template-columns:repeat(1,minmax(0,1fr))}@media (min-width:640px){.sm\:rounded-lg{border-radius:.5rem}.sm\:block{display:block}.sm\:items-center{align-items:center}.sm\:justify-start{justify-content:flex-start}.sm\:justify-between{justify-content:space-between}.sm\:h-20{height:5rem}.sm\:ml-0{margin-left:0}.sm\:px-6{padding-left:1.5rem;padding-right:1.5rem}.sm\:pt-0{padding-top:0}.sm\:text-left{text-align:left}.sm\:text-right{text-align:right}}@media (min-width:768px){.md\:border-t-0{border-top-width:0}.md\:border-l{border-left-width:1px}.md\:grid-cols-2{grid-template-columns:repeat(2,minmax(0,1fr))}}@media (min-width:1024px){.lg\:px-8{padding-left:2rem;padding-right:2rem}}@media (prefers-color-scheme:dark){.dark\:bg-gray-800{--bg-opacity:1;background-color:#2d3748;background-color:rgba(45,55,72,var(--bg-opacity))}.dark\:bg-gray-900{--bg-opacity:1;background-color:#1a202c;background-color:rgba(26,32,44,var(--bg-opacity))}.dark\:border-gray-700{--border-opacity:1;border-color:#4a5568;border-color:rgba(74,85,104,var(--border-opacity))}.dark\:text-white{--text-opacity:1;color:#fff;color:rgba(255,255,255,var(--text-opacity))}.dark\:text-gray-400{--text-opacity:1;color:#cbd5e0;color:rgba(203,213,224,var(--text-opacity))}.dark\:text-gray-500{--tw-text-opacity:1;color:#6b7280;color:rgba(107,114,128,var(--tw-text-opacity))}}
        </style>

        <style>
            body {
                font-family: 'Dosis', sans-serif;
            }
            .nunito{
                font-family: Nunito;
            }
            .dosis{
                font-family: Dosis;
            }
        </style>
    </head>
    <body class="antialiased">
        <div class="relative flex items-top justify-center min-h-screen bg-gray-100 dark:bg-gray-900 sm:items-center py-4 sm:pt-0" id="main_body">
            <div class="max-w-6xl mx-auto sm:px-12 lg:px-8 Nunito">
                <div class="flex justify-center pt-8 sm:justify-start sm:pt-0 dark:text-white nunito">
                    {{$task -> code}}: {{$task->topic}}
                </div>
                <div class="flex justify-center sm:justify-start sm:pt-0 dark:text-white nunito">
                {{$task -> unit}}
                {{$task -> type}}
                </div>
                <div class="flex justify-center sm:justify-start sm:pt-0 dark:text-white nunito" style="margin-bottom: 1rem;">
                {{"Difficulty:" }}
                {{$task -> difficulty}} 
                {{"/10"}}
                </div>
                <div class="m bg-white dark:bg-gray-800 overflow-hidden shadow sm:rounded-lg dosis">
                    <div class="grid grid-cols-1 md:grid-cols-2">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="ml-4 text-lg leading-7 font-semibold"><span href="3" class="underline text-gray-900 dark:text-white">Terms</span></div>
                            </div>

                            <div>
                                <div class="mt-2 text-gray-600 dark:text-gray-400 text-sm">
                                    @if($task -> pages)
                                    <span href="#"class="text-gray-900 dark:text-white">
                                        {{$task->pages}} {{" Pages "}} @
                                    </span> 
                                    <span href="#"class="text-gray-900 dark:text-white">
                                        {{$task->page_cost}} {{"KES"}}</br>
                                    </span>
                                    @endif
                                    <span href="#"class="text-gray-900 dark:text-white">
                                        {{$task -> full_pay}}{{" KES"}}
                                    </span><br>
                                    @if(!$task -> pages)
                                    <span href="#"class="text-gray-900 dark:text-white">
                                        {{"For whole task"}}
                                    </span></br>
                                    
                                    @endif 
                                    
                                    <span href="#"class="text-gray-900 dark:text-white">
                                        {{"Payment on Approval"}}
                                    </span></br>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="dosis bg-white dark:bg-gray-800 overflow-hidden shadow sm:rounded-lg">
                    <div class="grid grid-cols-1 md:grid-cols-2">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="ml-4 text-lg leading-7 font-semibold"><span href="3" class="underline text-gray-900 dark:text-white">Broker</span></div>
                            </div>

                            <div>
                                <div class="mt-2 text-gray-600 dark:text-gray-400 text-sm">
                                    <span href="#">
                                        {{$broker -> username}} 
                                    </span> </br>
                                    <span href="#">
                                        {{"Level: 23"}} 
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="dosis bg-white dark:bg-gray-800 overflow-hidden shadow sm:rounded-lg">
                    <div class="grid grid-cols-1 md:grid-cols-2">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="ml-4 text-lg leading-7 font-semibold"><span href="https://laravel.com/docs" class="underline text-gray-900 dark:text-white">Instructions</span></div>
                            </div>

                            <div>
                                <div class="mt-2 text-gray-600 dark:text-gray-400 text-sm">
                                    {{$task -> instructions}}
                                </div>
                            </div>
                        </div>
                            
                        <div class="p-6 border-t border-gray-200 dark:border-gray-700 md:border-t-0 md:border-l">
                            <div class="flex items-center">
                                <div class="ml-4 text-lg leading-7 font-semibold"><span href="https://laravel.com/docs" class="underline text-gray-900 dark:text-white">Files</span></div>
                            </div>
                        </div>
                        
                        <div class="flex items-top justify-center mx-auto sm:px-12 lg:px-8" id="timer"  style="color: red; font-weight: 900; font-size: 1.25rem;">
                            Time Remaining
                        </div>
                        <button class="nunito" style="padding: 1rem; background-color: green; margin-left : 1rem; margin-right : 1rem;
                         font-size: 1.5rem; font-weight: 900; color: white;">
                            Bid Now
                        <button>
                    </div>
                </div>

                <div class="flex justify-center sm:items-center sm:justify-between">
                    <div class="text-center text-sm text-gray-500 sm:text-left">
                        <div class="flex items-center">

                            <a href="https://laravel.bigcartel.com" class="underline">
                                Broker
                            </a>


                            <a href="https://github.com/sponsors/taylorotwell" target="_blank" class="ml-4 underline">
                                Bid
                            </a>
                        </div>
                    </div>

                    <div class="ml-4 text-center text-sm text-gray-500 sm:text-right sm:ml-0 underline">
                        Kazibin v0.00.1 
                    </div>
                </div>
            </div>
        </div>
    </body>
    <script>
        console.log(due_time)
        var countDownDate = new Date(due_time).getTime();

        // Update the count down every 1 second
        var x = setInterval(function() {

        // Get today's date and time
        var now = new Date().getTime();

        // Find the distance between now and the count down date
        var distance = countDownDate - now;

        // Time calculations for days, hours, minutes and seconds
        var days = Math.floor(distance / (1000 * 60 * 60 * 24));
        var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        // var seconds = Math.floor((distance % (1000 * 60)) / 1000);

        // Display the result in the element with id="timer"
        var stub_1 = (days > 0) ? days + "d " : ''
        var stub_2 = (hours > 0) ? hours + "h " : ''
        var stub_3 = (minutes > 0) ? minutes + "m " : ''
        // var stub_4 = seconds + "s "
        document.getElementById("timer").innerHTML = 'Due in: ' +  stub_1 + stub_2 + stub_3 

        // If the count down is finished, write some text
        if (distance < 0) {
            clearInterval(x);
            document.getElementById("timer").innerHTML = "Expired"
            document.getElementById("main_body").innerHTML = `<div class="max-w-6xl mx-auto sm:px-12 lg:px-8 Nunito">
                <div class="flex justify-center pt-8 sm:justify-start sm:pt-0 dark:text-white nunito">
                    {{$task -> code}}: {{$task->topic}}
                </div>
                <div class="flex justify-center sm:justify-start sm:pt-0 dark:text-white nunito">
                    Task Unavailable
                </div>
                

                <div class="dosis bg-white dark:bg-gray-800 overflow-hidden shadow sm:rounded-lg">
                    <div class="grid grid-cols-1 md:grid-cols-2">
                        <div class="p-6">
                            <div>
                                <div class="mt-2 text-gray-600 dark:text-gray-400 text-sm">
                                    <span href="#">
                                        {{"The task may be taken or might have exipired"}} 
                                    </span> </br>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="dosis bg-white dark:bg-gray-800 overflow-hidden shadow sm:rounded-lg">
                    <div class="grid grid-cols-1 md:grid-cols-2">
                        <button class="nunito" style="padding: 1rem; background-color: green; margin-left : 1rem; margin-right : 1rem;
                         font-size: 1.5rem; font-weight: 900; color: white;">
                            Explore more tasks
                        <button>
                    </div>
                </div>

                <div class="flex justify-center sm:items-center sm:justify-between">
                    <div class="text-center text-sm text-gray-500 sm:text-left">
                        <div class="flex items-center">
                            <a href="https://laravel.bigcartel.com" class="underline">
                                TnCs
                            </a>
                        </div>
                    </div>

                    <div class="ml-4 text-center text-sm text-gray-500 sm:text-right sm:ml-0 underline">
                        Kazibin v0.00.1 
                    </div>
                </div>
            </div>`;
        }
        }, 1000);
        </script>
    </script>
</html>
