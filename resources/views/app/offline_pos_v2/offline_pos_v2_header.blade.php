<header class="bg-white shadow-sm sticky top-5 z-30 rounded-lg m-5 border border-gray-100">
    <div class="mx-8 py-5">
        <div class="flex items-center justify-between">
            <div class="mr-12">
                <img src="{{ asset('logo/POS.png') }}" alt="JEZ POS" class="h-11 w-auto">
            </div>
            <div class="w-5/6 flex items-center gap-4">
                <!-- Real-time Clock -->
                <div class="bg-cyan rounded-lg text-white px-3 py-1 font-semibold text-sm flex items-center gap-2 cursor-pointer" id="clock-container" title="">
                    <i class="cft-standard-stroke cft-clock text-white text-lg"></i>
                    <span id="current-time" class="text-base">00:00:00</span>
                </div>
                <!-- Division Select -->
                <div class="flex flex-col gap-1">

                    <select class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-red-500 dark:focus:border-red-500"
                            id="std_id">
                        <option value="17" selected>OFFLINE</option>
                        <option value="14">ONLINE / WHATSAPP</option>
                        <option value="18">TIKTOK</option>
                        <option value="19">Shopee</option>
                    </select>
                </div>
                <div class="flex flex-col gap-1">
                    <select class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-red-500 dark:focus:border-red-500" id="item_type">
                        <option value="waiting">WAITING</option>
                        <option value="store" selected>STORE</option>
                        <option value="b1g1">B1G1</option>
                    </select>
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 relative">
                <!-- Shift Employee Button -->
                <button id="shift-btn" class="w-10 h-10 rounded-lg bg-green-400 hover:bg-green-500 flex items-center justify-center transition-colors" title="Shift Employee">
                    <i class="cft-standard-stroke cft-clock text-white text-lg"></i>
                </button>
                <!-- Folder Data Button -->
                <button id="folder-btn" class="w-10 h-10 rounded-lg bg-gray-200 hover:bg-gray-300 flex items-center justify-center transition-colors" title="Data Folder">
                    <i class="cft-standard-stroke cft-folder text-gray-700 text-lg"></i>
                </button>
                <!-- Calculator Button -->
                <div class="relative">
                    <button id="calculatorButton" class="w-10 h-10 rounded-lg bg-calculator hover:bg-calculator-dark flex items-center justify-center text-gray-600 transition-colors" title="Calculator">
                        <i class="cft-standard-stroke cft-calculator text-white text-lg"></i>
                    </button>
                    <!-- Calculator Dropdown -->
                    <div id="calculatorDropdown"
                         class="hidden absolute right-0 mt-2 z-50 bg-white rounded-2xl shadow-xl w-80 p-4 border border-gray-200">

                        <div class="calculator-container space-y-4">

                            <!-- DISPLAY -->
                            <div id="calculator-input"
                                 class="w-full h-20 bg-gray-50 border border-gray-300 rounded-xl px-4
                    text-right text-3xl font-semibold text-gray-900
                    flex items-center justify-end">
                                0
                            </div>

                            <!-- KEYPAD -->
                            <div class="grid grid-cols-4 gap-3">

                                <!-- ROW 1 -->
                                <button class="calc-btn bg-red-500 text-white min-h-[52px] text-lg rounded-xl" data-value="C">C</button>
                                <button class="calc-btn bg-red-100 text-red-700 min-h-[52px] text-lg rounded-xl" data-value="÷">÷</button>
                                <button class="calc-btn bg-red-100 text-red-700 min-h-[52px] text-lg rounded-xl" data-value="×">×</button>
                                <button class="calc-btn bg-red-100 text-red-700 min-h-[52px] text-lg rounded-xl" data-value="-">−</button>

                                <!-- ROW 2 -->
                                <button class="calc-btn bg-gray-100 min-h-[52px] text-lg rounded-xl" data-value="7">7</button>
                                <button class="calc-btn bg-gray-100 min-h-[52px] text-lg rounded-xl" data-value="8">8</button>
                                <button class="calc-btn bg-gray-100 min-h-[52px] text-lg rounded-xl" data-value="9">9</button>
                                <button class="calc-btn bg-red-100 text-red-700 min-h-[108px] text-lg rounded-xl row-span-2" data-value="+">+</button>

                                <!-- ROW 3 -->
                                <button class="calc-btn bg-gray-100 min-h-[52px] text-lg rounded-xl" data-value="4">4</button>
                                <button class="calc-btn bg-gray-100 min-h-[52px] text-lg rounded-xl" data-value="5">5</button>
                                <button class="calc-btn bg-gray-100 min-h-[52px] text-lg rounded-xl" data-value="6">6</button>

                                <!-- ROW 4 -->
                                <button class="calc-btn bg-gray-100 min-h-[52px] text-lg rounded-xl" data-value="1">1</button>
                                <button class="calc-btn bg-gray-100 min-h-[52px] text-lg rounded-xl" data-value="2">2</button>
                                <button class="calc-btn bg-gray-100 min-h-[52px] text-lg rounded-xl" data-value="3">3</button>
                                <button class="calc-btn bg-red-500 text-white min-h-[108px] text-lg rounded-xl row-span-2" data-value="=">=</button>

                                <!-- ROW 5 -->
                                <button class="calc-btn bg-gray-100 min-h-[52px] text-lg rounded-xl col-span-2" data-value="0">0</button>
                                <button class="calc-btn bg-gray-100 min-h-[52px] text-lg rounded-xl" data-value=".">.</button>

                            </div>

                            <!-- ADD AMOUNT -->
                            <button id="add_custom_amount"
                                    class="w-full min-h-[52px] bg-red-500 hover:bg-red-600
                       text-white rounded-xl text-lg font-medium transition">
                                Add Custom Amount
                            </button>

                        </div>
                    </div>
                </div>
                <button id="dropdownUserButton" data-dropdown-toggle="dropdownUser" class="w-10 h-10 rounded-full hover:bg-gray-100 flex items-center justify-center overflow-hidden transition-colors">
                    @php
                        $userName = $data['user']->u_name ?? 'User';
                        $fallbackUrl = 'https://ui-avatars.com/api/?name=' . urlencode($userName) . '&background=F74040&color=fff';
                        $logoPath = file_exists(public_path('logo/logo.png')) ? asset('logo/logo.png') : $fallbackUrl;
                    @endphp
                    <img src="{{ $logoPath }}" alt="User" class="w-10 h-10 rounded-full object-cover" onerror="this.onerror=null; this.src='{{ $fallbackUrl }}'">
                </button>
                <!-- Dropdown menu -->
                <div id="dropdownUser" class="hidden z-10 bg-white divide-y divide-gray-100 rounded-lg shadow w-64 mr-4">
                    <div class="px-4 py-3 text-sm text-gray-900">
                        <div class="font-medium">{{ $data['user']->u_name ?? 'User' }}</div>
                    </div>
                    <ul class="py-2 text-sm text-gray-700">
                        <li>
                            <a href="{{ route('logout') }}" class="block px-4 py-2 hover:bg-gray-100">Logout</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</header>