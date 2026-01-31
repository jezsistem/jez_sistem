@extends('layouts.app_v2')

@section('content')
<div class="space-y-6">
    <!-- Welcome Header -->
    <div>
        <h1 class="text-xl font-semibold text-gray-900">Hi, {{ Auth::user()->u_name ?? 'User' }}</h1>
        <p class="text-sm text-gray-500 mt-1">This is HR Dashboard</p>
    </div>
    
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Employees -->
        <div class="bg-white rounded-xl p-6 border border-gray-200 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-blue-50 rounded-lg flex items-center justify-center">
                    <i class="cft-standard-stroke cft-user text-blue-600 text-2xl"></i>
                </div>
                <span class="px-2.5 py-0.5 bg-red-50 text-red-500 text-xs font-semibold rounded-full flex items-center gap-1">
                    <i class="fas fa-arrow-up text-xs"></i>
                    +25.5%
                </span>
            </div>
            <h3 class="text-3xl font-bold text-gray-900">120</h3>
            <p class="text-sm text-gray-500 mt-1">Total Employees</p>
        </div>
        
        <!-- Job Applicants -->
        <div class="bg-white rounded-xl p-6 border border-gray-200 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-green-50 rounded-lg flex items-center justify-center">
                    <i class="cft-standard-stroke cft-briefcase text-green-600 text-2xl"></i>
                </div>
                <span class="px-2.5 py-0.5 bg-red-50 text-red-500 text-xs font-semibold rounded-full flex items-center gap-1">
                    <i class="fas fa-arrow-up text-xs"></i>
                    +4.10%
                </span>
            </div>
            <h3 class="text-3xl font-bold text-gray-900">40</h3>
            <p class="text-sm text-gray-500 mt-1">Job Applicants</p>
        </div>
        
        <!-- New Employees -->
        <div class="bg-white rounded-xl p-6 border border-gray-200 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-purple-50 rounded-lg flex items-center justify-center">
                    <i class="fas fa-plus text-purple-600 text-2xl"></i>
                </div>
                <span class="px-2.5 py-0.5 bg-red-50 text-red-500 text-xs font-semibold rounded-full flex items-center gap-1">
                    <i class="fas fa-arrow-up text-xs"></i>
                    +5.1%
                </span>
            </div>
            <h3 class="text-3xl font-bold text-gray-900">4</h3>
            <p class="text-sm text-gray-500 mt-1">New Employees</p>
        </div>
        
        <!-- Resigned Employees -->
        <div class="bg-white rounded-xl p-6 border border-gray-200 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-red-50 rounded-lg flex items-center justify-center">
                    <i class="fas fa-minus text-red-500 text-2xl"></i>
                </div>
                <span class="px-2.5 py-0.5 bg-red-50 text-red-500 text-xs font-semibold rounded-full flex items-center gap-1">
                    <i class="fas fa-arrow-down text-xs"></i>
                    +25.5%
                </span>
            </div>
            <h3 class="text-3xl font-bold text-gray-900">8</h3>
            <p class="text-sm text-gray-500 mt-1">Resigned Employees</p>
        </div>
    </div>
    
    <!-- Team Performance Chart & Total Employee Pie Chart -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Team Performance (2 columns) -->
        <div class="lg:col-span-2 bg-white rounded-xl p-6 border border-gray-200">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Team Performance</h3>
                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 bg-red-500 rounded-full"></span>
                        <span class="text-sm text-gray-600">Project Team</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 bg-yellow-500 rounded-full"></span>
                        <span class="text-sm text-gray-600">Product Team</span>
                    </div>
                    <button class="px-3 py-1.5 text-sm border border-gray-300 rounded-lg hover:bg-gray-50 flex items-center gap-2">
                        Last 7 month
                        <i class="fas fa-calendar text-xs"></i>
                    </button>
                </div>
            </div>
            <div class="h-64">
                <canvas id="teamPerformanceChart"></canvas>
            </div>
        </div>
        
        <!-- Total Employee Pie Chart (1 column) -->
        <div class="bg-white rounded-xl p-6 border border-gray-200">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Total Employee</h3>
                <select class="text-sm border border-gray-300 rounded-lg px-3 py-1.5">
                    <option>All Time</option>
                    <option>This Month</option>
                    <option>This Year</option>
                </select>
            </div>
            <div class="flex items-center justify-center h-48 mb-6">
                <canvas id="totalEmployeeChart"></canvas>
            </div>
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 bg-red-500 rounded-full"></span>
                        <span class="text-sm text-gray-600">Others</span>
                    </div>
                    <span class="text-sm font-semibold text-gray-900">71</span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 bg-yellow-500 rounded-full"></span>
                        <span class="text-sm text-gray-600">Onboarding</span>
                    </div>
                    <span class="text-sm font-semibold text-gray-900">27</span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 bg-blue-500 rounded-full"></span>
                        <span class="text-sm text-gray-600">Offboarding</span>
                    </div>
                    <span class="text-sm font-semibold text-gray-900">23</span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Employees Table -->
    <div class="bg-white rounded-xl border border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Employees</h3>
                <div class="flex items-center gap-3">
                    <!-- Search -->
                    <div class="relative">
                        <input type="text" placeholder="Search employee" class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    </div>
                    
                    <!-- Filters -->
                    <select class="px-4 py-2 border border-gray-300 rounded-lg text-sm">
                        <option>All Division</option>
                        <option>Team Product</option>
                        <option>Team Sales</option>
                    </select>
                    <select class="px-4 py-2 border border-gray-300 rounded-lg text-sm">
                        <option>All Position</option>
                        <option>Manager</option>
                        <option>Staff</option>
                    </select>
                    <select class="px-4 py-2 border border-gray-300 rounded-lg text-sm">
                        <option>All Status</option>
                        <option>Active</option>
                        <option>Inactive</option>
                    </select>
                </div>
            </div>
        </div>
        
        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left">
                            <input type="checkbox" class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-2 focus:ring-blue-500">
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Position</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Division</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Store</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <input type="checkbox" class="w-4 h-4 text-blue-600 rounded border-gray-300">
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <img src="https://ui-avatars.com/api/?name=Pristia+Candra&background=F74040&color=fff" class="w-10 h-10 rounded-full" alt="">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Pristia Candra</p>
                                    <p class="text-xs text-gray-500">pristia@jezpro.id</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">UI UX Designer</td>
                        <td class="px-6 py-4 text-sm text-gray-500">ae@jezpro.id</td>
                        <td class="px-6 py-4 text-sm text-gray-900">Team Product</td>
                        <td class="px-6 py-4 text-sm text-gray-900">Head Office</td>
                    </tr>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <input type="checkbox" class="w-4 h-4 text-blue-600 rounded border-gray-300">
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <img src="https://ui-avatars.com/api/?name=Hanna+Baptista&background=3B82F6&color=fff" class="w-10 h-10 rounded-full" alt="">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Hanna Baptista</p>
                                    <p class="text-xs text-gray-500">hanna@jezpro.id</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">Graphic Designer</td>
                        <td class="px-6 py-4 text-sm text-gray-500">ae@jezpro.id</td>
                        <td class="px-6 py-4 text-sm text-gray-900">Team Product</td>
                        <td class="px-6 py-4 text-sm text-gray-900">Head Office</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Team Performance Chart
const teamCtx = document.getElementById('teamPerformanceChart').getContext('2d');
new Chart(teamCtx, {
    type: 'line',
    data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
        datasets: [{
            label: 'Project Team',
            data: [42000, 45000, 41000, 48000, 52000, 47000, 53000],
            borderColor: '#EF4444',
            backgroundColor: 'rgba(239, 68, 68, 0.1)',
            tension: 0.4,
            fill: true
        }, {
            label: 'Product Team',
            data: [38000, 43000, 40000, 46000, 48000, 44000, 41000],
            borderColor: '#F59E0B',
            backgroundColor: 'rgba(245, 158, 11, 0.1)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: false,
                ticks: {
                    callback: function(value) {
                        return value / 1000 + 'k';
                    }
                }
            }
        }
    }
});

// Total Employee Pie Chart
const pieCtx = document.getElementById('totalEmployeeChart').getContext('2d');
new Chart(pieCtx, {
    type: 'doughnut',
    data: {
        labels: ['Others', 'Onboarding', 'Offboarding'],
        datasets: [{
            data: [71, 27, 23],
            backgroundColor: ['#EF4444', '#F59E0B', '#3B82F6'],
            borderWidth: 0
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        cutout: '70%'
    }
});
</script>
@endpush

