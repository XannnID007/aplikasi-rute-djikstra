@extends('layouts.admin')

@section('title', 'Daftar Pengguna')
@section('page-title', 'Daftar Pengguna')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Daftar Pengguna</h1>
                <p class="text-gray-600 mt-1">Kelola semua pengguna yang terdaftar dalam sistem</p>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-xl shadow-md p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">Total Pengguna</p>
                        <p class="text-3xl font-bold text-blue-600">{{ $pengguna->total() }}</p>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-lg">
                        <i class="fas fa-users text-2xl text-blue-600"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-md p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">Pengguna Aktif Hari Ini</p>
                        <p class="text-3xl font-bold text-green-600">
                            {{ \App\Models\PencarianRute::whereDate('created_at', today())->distinct('user_id')->count() }}
                        </p>
                    </div>
                    <div class="bg-green-100 p-3 rounded-lg">
                        <i class="fas fa-user-check text-2xl text-green-600"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-md p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">Pendaftar Baru (30 hari)</p>
                        <p class="text-3xl font-bold text-purple-600">
                            {{ \App\Models\User::where('peran', 'pengguna')->where('created_at', '>=', now()->subDays(30))->count() }}
                        </p>
                    </div>
                    <div class="bg-purple-100 p-3 rounded-lg">
                        <i class="fas fa-user-plus text-2xl text-purple-600"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Users Table -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Data Pengguna</h3>
            </div>

            @if ($pengguna->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    No</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Pengguna</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Total Pencarian</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Terakhir Aktif</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Terdaftar</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($pengguna as $index => $user)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $pengguna->firstItem() + $index }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div
                                                class="bg-blue-600 text-white rounded-full h-10 w-10 flex items-center justify-center mr-3">
                                                <span
                                                    class="font-medium text-sm">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                            </div>
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                                <div class="text-sm text-gray-500">ID: {{ $user->id }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $user->email }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $user->pencarianRute->count() }} pencarian
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        @if ($user->pencarianRute->count() > 0)
                                            @php
                                                $lastSearch = $user->pencarianRute->sortByDesc('created_at')->first();
                                            @endphp
                                            {{ $lastSearch->created_at->diffForHumans() }}
                                        @else
                                            <span class="text-gray-400">Belum pernah</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <div>{{ $user->created_at->format('d/m/Y') }}</div>
                                        <div class="text-xs text-gray-500">{{ $user->created_at->diffForHumans() }}</div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $pengguna->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <div class="bg-gray-100 p-4 rounded-full w-16 h-16 mx-auto mb-4 flex items-center justify-center">
                        <i class="fas fa-users text-2xl text-gray-400"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada pengguna</h3>
                    <p class="text-gray-600">Pengguna akan muncul di sini setelah mereka mendaftar</p>
                </div>
            @endif
        </div>

        <!-- User Activity Chart -->
        <div class="bg-white rounded-xl shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-chart-bar text-green-600 mr-2"></i>
                Aktivitas Pengguna (7 Hari Terakhir)
            </h3>

            <div class="grid grid-cols-7 gap-2">
                @for ($i = 6; $i >= 0; $i--)
                    @php
                        $date = now()->subDays($i);
                        $count = \App\Models\PencarianRute::whereDate('created_at', $date)->count();
                        $maxHeight = 100;
                        $height = $count > 0 ? max(20, min($maxHeight, $count * 10)) : 10;
                    @endphp
                    <div class="text-center">
                        <div class="bg-blue-600 rounded-t mx-auto mb-2"
                            style="width: 20px; height: {{ $height }}px; min-height: 10px;"></div>
                        <div class="text-xs text-gray-600">{{ $date->format('d/m') }}</div>
                        <div class="text-xs font-medium text-gray-900">{{ $count }}</div>
                    </div>
                @endfor
            </div>

            <div class="mt-4 text-center text-sm text-gray-600">
                Total pencarian dalam 7 hari terakhir:
                <span class="font-medium text-gray-900">
                    {{ \App\Models\PencarianRute::whereBetween('created_at', [now()->subDays(6)->startOfDay(), now()->endOfDay()])->count() }}
                </span>
            </div>
        </div>
    </div>
@endsection
