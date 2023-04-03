<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Dosis:wght@200;300;400&display=swap" rel="stylesheet">
        <!-- Styles -->
        <style>
            /*! normalize.css v8.0.1 | MIT License | github.com/necolas/normalize.css */html{line-height:1.15;-webkit-text-size-adjust:100%}body{margin:0}a{background-color:transparent}[hidden]{display:none}html{font-family:system-ui,-apple-system,BlinkMacSystemFont,Segoe UI,Roboto,Helvetica Neue,Arial,Noto Sans,sans-serif,Apple Color Emoji,Segoe UI Emoji,Segoe UI Symbol,Noto Color Emoji;line-height:1.5}*,:after,:before{box-sizing:border-box;border:0 solid #e2e8f0}a{color:inherit;text-decoration:inherit}svg,video{display:block;vertical-align:middle}video{max-width:100%;height:auto}.bg-white{--bg-opacity:1;background-color:#fff;background-color:rgba(255,255,255,var(--bg-opacity))}.bg-gray-100{--bg-opacity:1;background-color:#f7fafc;background-color:rgba(247,250,252,var(--bg-opacity))}.border-gray-200{--border-opacity:1;border-color:#edf2f7;border-color:rgba(237,242,247,var(--border-opacity))}.border-t{border-top-width:1px}.flex{display:flex}.grid{display:grid}.hidden{display:none}.items-center{align-items:center}.justify-center{justify-content:center}.font-semibold{font-weight:600}.h-5{height:1.25rem}.h-8{height:2rem}.h-16{height:4rem}.text-sm{font-size:.875rem}.text-lg{font-size:1.125rem}.leading-7{line-height:1.75rem}.mx-auto{margin-left:auto;margin-right:auto}.ml-1{margin-left:.25rem}.mt-2{margin-top:.5rem}.mr-2{margin-right:.5rem}.ml-2{margin-left:.5rem}.mt-4{margin-top:1rem}.ml-4{margin-left:1rem}.mt-8{margin-top:2rem}.ml-12{margin-left:3rem}.-mt-px{margin-top:-1px}.max-w-6xl{max-width:72rem}.min-h-screen{min-height:100vh}.overflow-hidden{overflow:hidden}.p-6{padding:1.5rem}.py-4{padding-top:1rem;padding-bottom:1rem}.px-6{padding-left:1.5rem;padding-right:1.5rem}.pt-8{padding-top:2rem}.fixed{position:fixed}.relative{position:relative}.top-0{top:0}.right-0{right:0}.shadow{box-shadow:0 1px 3px 0 rgba(0,0,0,.1),0 1px 2px 0 rgba(0,0,0,.06)}.text-center{text-align:center}.text-gray-200{--text-opacity:1;color:#edf2f7;color:rgba(237,242,247,var(--text-opacity))}.text-gray-300{--text-opacity:1;color:#e2e8f0;color:rgba(226,232,240,var(--text-opacity))}.text-gray-400{--text-opacity:1;color:#cbd5e0;color:rgba(203,213,224,var(--text-opacity))}.text-gray-500{--text-opacity:1;color:#a0aec0;color:rgba(160,174,192,var(--text-opacity))}.text-gray-600{--text-opacity:1;color:#718096;color:rgba(113,128,150,var(--text-opacity))}.text-gray-700{--text-opacity:1;color:#4a5568;color:rgba(74,85,104,var(--text-opacity))}.text-gray-900{--text-opacity:1;color:#1a202c;color:rgba(26,32,44,var(--text-opacity))}.underline{text-decoration:underline}.antialiased{-webkit-font-smoothing:antialiased;-moz-osx-font-smoothing:grayscale}.w-5{width:1.25rem}.w-8{width:2rem}.w-auto{width:auto}.grid-cols-1{grid-template-columns:repeat(1,minmax(0,1fr))}@media (min-width:640px){.sm\:rounded-lg{border-radius:.5rem}.sm\:block{display:block}.sm\:items-center{align-items:center}.sm\:justify-start{justify-content:flex-start}.sm\:justify-between{justify-content:space-between}.sm\:h-20{height:5rem}.sm\:ml-0{margin-left:0}.sm\:px-6{padding-left:1.5rem;padding-right:1.5rem}.sm\:pt-0{padding-top:0}.sm\:text-left{text-align:left}.sm\:text-right{text-align:right}}@media (min-width:768px){.md\:border-t-0{border-top-width:0}.md\:border-l{border-left-width:1px}.md\:grid-cols-2{grid-template-columns:repeat(2,minmax(0,1fr))}}@media (min-width:1024px){.lg\:px-8{padding-left:2rem;padding-right:2rem}}@media (prefers-color-scheme:dark){.dark\:bg-gray-800{--bg-opacity:1;background-color:#2d3748;background-color:rgba(45,55,72,var(--bg-opacity))}.dark\:bg-gray-900{--bg-opacity:1;background-color:#1a202c;background-color:rgba(26,32,44,var(--bg-opacity))}.dark\:border-gray-700{--border-opacity:1;border-color:#4a5568;border-color:rgba(74,85,104,var(--border-opacity))}.dark\:text-white{--text-opacity:1;color:#fff;color:rgba(255,255,255,var(--text-opacity))}.dark\:text-gray-400{--text-opacity:1;color:#cbd5e0;color:rgba(203,213,224,var(--text-opacity))}.dark\:text-gray-500{--tw-text-opacity:1;color:#6b7280;color:rgba(107,114,128,var(--tw-text-opacity))}}
        </style>

        <style>
            body {
              font-family: 'Dosis';
              background-color: rgb(85, 85, 129);
            }
            .verify-button{
              background-color: green;
              color: white;
            }
            .icon{
                height: 5rem;
                width: 5rem;
            }
        </style>
    </head>
    <body class="antialiased">
        <div class="relative flex items-top justify-center min-h-screen  sm:items-center py-4 sm:pt-0" style="color: white;">

            <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
                <div class="flex justify-center pt-8 sm:justify-start sm:pt-0">
                <svg version="1.2" baseProfile="tiny" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                    x="0px" y="0px" viewBox="0 0 240 314" xml:space="preserve">
                    <path id="XMLID_15_" fill="#0E0F38" d="M483,747.9L483,747.9c-64.6,0-117-52.4-117-117v-72c0-64.6,52.4-117,117-117h0
                        c64.6,0,117,52.4,117,117v72C600,695.5,547.7,747.9,483,747.9z"/>
                    <rect id="_x3C_Slice_x3E_" x="676.3" y="152" fill="none" width="252" height="460"/>
                    <g id="XMLID_19_">
                        <path id="XMLID_20_" fill="#E5E65D" d="M413.2,486.3L413.2,486.3c0.7-9.8-5.2,54.6-0.7,55.9l138.3-33.7c4.5,1.3,7.5,10.3,6.7,20h0
                            c-0.7,9.8-5,16.6-9.4,15.3l-128.1-37.5C415.4,505,412.4,496,413.2,486.3z"/>
                    </g>
                    <path id="XMLID_18_" fill="#E5E65D" d="M517,685.5L517,685.5c-4.4-0.6-7.3-4.1-6.5-7.8l22.2-104.7c0.8-3.6,5-6.1,9.3-5.4l0,0
                        c4.4,0.6,7.3,4.1,6.5,7.8l-22.2,104.7C525.6,683.7,521.4,686.2,517,685.5z"/>
                    <path id="XMLID_17_" fill="#E5E65D" d="M453.9,684.1L453.9,684.1c-4.7,0.6-9.2-1.8-10.1-5.4l-24-104.7c-0.8-3.6,2.3-7.1,7.1-7.8h0
                        c4.7-0.6,9.2,1.8,10.1,5.4l24,104.7C461.8,680,458.6,683.4,453.9,684.1z"/>
                    <path id="XMLID_16_" fill="#E5E65D" d="M445.3,674.9l0.2-3.9c0.3-6.1,5.3-10.7,11.1-10.4l10,0.5c5.9,0.3,10.4,5.4,10.1,11.5
                        l-0.2,3.9c-0.3,6.1-5.3,10.7-11.1,10.4l-10-0.5C449.5,686.1,445,681,445.3,674.9z"/>
                    <rect id="XMLID_49_" x="484.3" y="584.9" fill="#E5E65D" width="2.6" height="99.7"/>
                    <path id="XMLID_50_" fill="#E5E65D" d="M504.5,685h-9.4c-0.7,0-1.3-0.6-1.3-1.3v-97.2c0-0.7,0.6-1.3,1.3-1.3h9.4
                        c0.7,0,1.3,0.6,1.3,1.3v97.2C505.8,684.4,505.2,685,504.5,685z"/>
                    <g id="XMLID_674_">
                        <rect id="XMLID_156_" x="606.2" y="494.9" fill="none" width="945.1" height="234"/>
                        <path id="XMLID_30_" fill="#E5E65D" d="M631.4,499.4c0-2.4,1.8-4.5,4.5-4.5h3.9c2.1,0,4.5,2.1,4.5,4.5v123.3l71.4-54.3
                            c2.1-1.5,3.9-2.1,5.7-2.1h9.9c3.3,0,3.6,4.2,0.9,6.3l-73.5,55.5l76.8,70.8c2.7,2.4,0.9,6-1.8,6h-7.8c-2.1,0-3.6-0.3-6.9-2.7
                            l-74.7-69.6v67.8c0,3-1.5,4.5-4.5,4.5h-4.5c-2.7,0-3.9-2.1-3.9-4.5V499.4z"/>
                        <path id="XMLID_139_" fill="#E5E65D" d="M814.1,615.5c15.9,0,32.1,5.1,35.7,6.3c0-30.6-3.9-46.2-31.2-46.2
                            c-22.8,0-38.7,8.4-41.4,9.3c-3,1.2-4.8,0-5.4-2.1l-1.5-4.2c-1.5-2.7,0.3-4.2,1.8-5.1c0.9-0.6,18.6-10.2,47.7-10.2
                            c42,0,43.2,24.6,43.2,60.3v76.8c0,2.4-2.4,4.5-4.8,4.5h-2.4c-2.4,0-3.9-1.2-4.2-3.9l-1.5-12.6c-8.7,8.7-26.7,19.8-48.6,19.8
                            c-25.8,0-45-17.4-45-45.9C756.5,635.6,777.8,615.5,814.1,615.5z M803,695.3c21,0,40.2-12.6,46.8-21v-39.9
                            c-4.5-2.1-19.2-7.2-35.7-7.2c-26.4,0-44.7,14.4-44.7,34.8C769.4,681.8,783.8,695.3,803,695.3z"/>
                        <path id="XMLID_143_" fill="#E5E65D" d="M897.2,700.4c0-2.1,0.6-3.6,1.8-5.4l81.9-114.9v-0.9H908c-2.4,0-4.2-2.1-4.2-4.2v-4.2
                            c0-3,1.8-4.5,4.2-4.5h89.7c2.7,0,4.5,2.1,4.5,4.5c0,0.6,0,1.5-1.8,3.9l-83.7,116.4v0.9h75.6c2.7,0,4.5,1.8,4.5,4.5v3.9
                            c0,2.4-1.8,4.5-4.5,4.5h-90.6c-2.7,0-4.5-1.5-4.5-3.9V700.4z"/>
                        <path id="XMLID_145_" fill="#E5E65D" d="M1031.9,516.5c0-6.6,5.4-11.7,12-11.7s11.7,5.1,11.7,11.7c0,6.3-5.1,11.4-11.7,11.4
                            S1031.9,522.8,1031.9,516.5z M1037.6,570.8c0-2.4,1.8-4.5,4.5-4.5h4.2c2.1,0,4.5,2.1,4.5,4.5v129.6c0,3-1.5,4.5-4.5,4.5h-4.8
                            c-2.7,0-3.9-2.1-3.9-4.5V570.8z"/>
                        <path id="XMLID_148_" fill="#E5E65D" d="M1162.1,708.2c-26.7,0-44.7-15.3-48.6-20.1l-0.9,12.3c-0.6,3.3-2.4,4.5-5.1,4.5h-1.8
                            c-2.1,0-4.5-2.1-4.5-4.5v-201c0-2.4,2.4-4.5,4.2-4.5h4.5c3,0,4.5,2.1,4.5,4.5v77.4c0,0,17.1-13.5,43.8-13.5
                            c39.3,0,66.9,32.4,66.9,72.3C1225.1,676.1,1198.1,708.2,1162.1,708.2z M1156.4,576.2c-25.2,0-42,15.6-42,15.6v80.1
                            c0.6,1.5,16.5,23.4,46.8,23.4c29.1,0,50.7-27.6,50.7-59.7C1211.9,602.9,1188.2,576.2,1156.4,576.2z"/>
                        <path id="XMLID_151_" fill="#E5E65D" d="M1260.5,516.5c0-6.6,5.4-11.7,12-11.7c6.6,0,11.7,5.1,11.7,11.7c0,6.3-5.1,11.4-11.7,11.4
                            C1265.9,527.9,1260.5,522.8,1260.5,516.5z M1266.2,570.8c0-2.4,1.8-4.5,4.5-4.5h4.2c2.1,0,4.5,2.1,4.5,4.5v129.6
                            c0,3-1.5,4.5-4.5,4.5h-4.8c-2.7,0-3.9-2.1-3.9-4.5V570.8z"/>
                        <path id="XMLID_154_" fill="#E5E65D" d="M1329.8,570.8c0-2.4,1.8-4.5,4.8-4.5h1.5c2.7,0,3.9,1.2,4.2,3.6l1.5,14.7
                            c4.5-4.8,21.3-21.3,48.9-21.3c39.3,0,52.2,24.3,52.2,60.6v76.5c0,2.4-2.1,4.5-4.5,4.5h-4.2c-2.4,0-4.5-2.1-4.5-4.5v-75.9
                            c0-32.1-12.9-48.3-39.6-48.3c-28.2,0-45.9,21.9-47.1,23.7v100.5c0,3-1.5,4.5-4.5,4.5h-4.8c-2.7,0-3.9-2.1-3.9-4.5V570.8z"/>
                    </g>
                    <path id="XMLID_1_" fill="#E5E65D" d="M484.4,509.5h-0.7c-5.3,0-9.6-4.3-9.6-9.6v-0.7c0-5.3,4.3-9.6,9.6-9.6h0.7
                        c5.3,0,9.6,4.3,9.6,9.6v0.7C494,505.2,489.7,509.5,484.4,509.5z"/>
                    <path id="XMLID_60_" fill="#0E0F38" d="M122.8,312.9L122.8,312.9c-64.6,0-117-52.4-117-117v-72c0-64.6,52.4-117,117-117h0
                        c64.6,0,117,52.4,117,117v72C239.8,260.5,187.4,312.9,122.8,312.9z"/>
                    <g id="XMLID_58_">
                        <path id="XMLID_59_" fill="#E5E65D" d="M52.9,51.3L52.9,51.3c0.7-9.8-5.2,54.6-0.7,55.9l138.3-33.7c4.5,1.3,7.5,10.3,6.7,20v0
                            c-0.7,9.8-5,16.6-9.4,15.3L59.6,71.3C55.2,70,52.2,61,52.9,51.3z"/>
                    </g>
                    <path id="XMLID_57_" fill="#E5E65D" d="M156.8,250.5L156.8,250.5c-4.4-0.6-7.3-4.1-6.5-7.8l22.2-104.7c0.8-3.6,5-6.1,9.3-5.4h0
                        c4.4,0.6,7.3,4.1,6.5,7.8l-22.2,104.7C165.3,248.7,161.1,251.1,156.8,250.5z"/>
                    <path id="XMLID_56_" fill="#E5E65D" d="M93.6,249.1L93.6,249.1c-4.7,0.6-9.2-1.8-10.1-5.4L59.5,139c-0.8-3.6,2.3-7.1,7.1-7.8h0
                        c4.7-0.6,9.2,1.8,10.1,5.4l24,104.7C101.5,244.9,98.4,248.4,93.6,249.1z"/>
                    <path id="XMLID_55_" fill="#E5E65D" d="M85,239.9l0.2-3.9c0.3-6.1,5.3-10.7,11.1-10.4l10,0.5c5.9,0.3,10.4,5.4,10.1,11.5l-0.2,3.9
                        c-0.3,6.1-5.3,10.7-11.1,10.4l-10-0.5C89.2,251.1,84.7,245.9,85,239.9z"/>
                    <rect id="XMLID_54_" x="124" y="149.9" fill="#E5E65D" width="2.6" height="99.7"/>
                    <path id="XMLID_53_" fill="#E5E65D" d="M144.2,249.9h-9.4c-0.7,0-1.3-0.6-1.3-1.3v-97.2c0-0.7,0.6-1.3,1.3-1.3h9.4
                        c0.7,0,1.3,0.6,1.3,1.3v97.2C145.5,249.4,144.9,249.9,144.2,249.9z"/>
                    <path id="XMLID_52_" fill="#E5E65D" d="M124.1,74.5h-0.7c-5.3,0-9.6-4.3-9.6-9.6v-0.7c0-5.3,4.3-9.6,9.6-9.6h0.7
                        c5.3,0,9.6,4.3,9.6,9.6v0.7C133.8,70.2,129.5,74.5,124.1,74.5z"/>
                    <g id="XMLID_26_">
                    </g>
                    <g id="XMLID_140_">
                    </g>
                    <g id="XMLID_157_">
                    </g>
                    <g id="XMLID_158_">
                    </g>
                    <g id="XMLID_159_">
                    </g>
                </svg>

                </div>

                <div class="mt-8 bg-white dark:bg-gray-800 overflow-hidden shadow sm:rounded-lg">
                    <div class="grid grid-cols-1 md:grid-cols-2">
                        <div class="p-6">
                            <div class="flex items-center">
                                <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" class="w-8 h-8 text-gray-500"><path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                <div class="ml-4 text-lg leading-7 font-semibold">
                                  Hello {{$user->username}}
                                </div>
                            </div>

                            <div class="ml-12">
                              <div class="mt-2 text-gray-600 dark:text-gray-400 text-sm">
                                Thank you for registering with kazibin
                              </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-center mt-4 sm:items-center sm:justify-between">
                    <div class="text-center text-sm text-gray-500 sm:text-left">
                        <div class="flex items-center">

                            <a href="{{env('APP_CLIENT') . '/verify_email/' . $user->email_verification }}" class="ml-1 underline">
                                Click Here to Verify Email
                            </a>
                        </div>
                    </div>

                    <div class="ml-4 text-center text-sm text-gray-500 sm:text-right sm:ml-0">
                        {{config('app.name')}} v{{ 0.1.0 }}
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
