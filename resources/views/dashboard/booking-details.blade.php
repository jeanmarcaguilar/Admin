@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-100">
    @include('layouts.navigation')

    <!-- Page Heading -->
    @if (isset($header))
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                {{ $header }}
            </div>
        </header>
    @endif

    <!-- Page Content -->
    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Breadcrumb -->
            <nav class="flex mb-6" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                            Dashboard
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                            </svg>
                            <a href="{{ route('bookings.index') }}" class="ml-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ml-2">
                                Bookings
                            </a>
                        </div>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <svg class="w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                            </svg>
                            <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">
                                {{ $booking['name'] }}
                            </span>
                        </div>
                    </li>
                </ol>
            </nav>

            <!-- Header with Actions -->
            <div class="md:flex md:items-center md:justify-between mb-8">
                <div class="flex-1 min-w-0">
                    <h1 class="text-3xl font-bold leading-tight text-gray-900">
                        Booking Details
                    </h1>
                    <p class="mt-2 text-sm text-gray-600">
                        Booking #{{ $booking['id'] }} • Created {{ \Carbon\Carbon::parse($booking['created_at'])->diffForHumans() }}
                    </p>
                </div>
                <div class="mt-4 flex md:mt-0 md:ml-4 space-x-3">
                    @if($booking['status'] !== 'cancelled' && $booking['status'] !== 'completed')
                        @if(auth()->user()->can('update', $booking))
                            <a href="{{ route('booking.edit', $booking['id']) }}" 
                               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg class="mr-2 h-4 w-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                Edit
                            </a>
                        @endif
                    @endif
                    
                    @if(auth()->user()->can('delete', $booking))
                        <form action="{{ route('booking.cancel', $booking['id']) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500" 
                                    onclick="return confirm('Are you sure you want to cancel this booking? This action cannot be undone.')">
                                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Cancel
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Left Column: Booking Details -->
                <div class="lg:col-span-2">
                    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                        <!-- Status Banner -->
                        <div class="px-4 py-5 sm:px-6 border-b border-gray-200 bg-gray-50">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                                        {{ $booking['name'] }}
                                    </h3>
                                    <p class="mt-1 max-w-2xl text-sm text-gray-500">
                                        {{ $booking['type'] === 'room' ? 'Room Booking' : 'Equipment Reservation' }}
                                    </p>
                                </div>
                                <div>
                                    @php
                                        $statusClasses = [
                                            'confirmed' => 'bg-green-100 text-green-800 border-green-200',
                                            'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                            'cancelled' => 'bg-red-100 text-red-800 border-red-200',
                                            'completed' => 'bg-blue-100 text-blue-800 border-blue-200'
                                        ];
                                        $statusClass = $statusClasses[$booking['status']] ?? 'bg-gray-100 text-gray-800 border-gray-200';
                                    @endphp
                                    <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full border {{ $statusClass }}">
                                        {{ ucfirst($booking['status']) }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Booking Details -->
                        <div class="border-t border-gray-200">
                            <dl class="divide-y divide-gray-200">
                                <!-- Booking Period -->
                                <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500">
                                        Booking Period
                                    </dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                        <div class="flex items-center space-x-4">
                                            <div class="flex items-center">
                                                <svg class="mr-2 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                                <span>
                                                    {{ \Carbon\Carbon::parse($booking['date'])->format('F j, Y') }}
                                                    @if(isset($booking['return_date']) && $booking['return_date'] !== $booking['date'])
                                                        - {{ \Carbon\Carbon::parse($booking['return_date'])->format('F j, Y') }}
                                                    @endif
                                                </span>
                                            </div>
                                            @if(isset($booking['start_time']))
                                            <div class="flex items-center">
                                                <svg class="mr-2 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                <span>
                                                    {{ \Carbon\Carbon::parse($booking['start_time'])->format('h:i A') }} - 
                                                    {{ \Carbon\Carbon::parse($booking['end_time'])->format('h:i A') }}
                                                </span>
                                            </div>
                                            @endif
                                        </div>
                                    </dd>
                                </div>

                                <!-- Resource Details -->
                                <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500">
                                        {{ $booking['type'] === 'room' ? 'Room' : 'Equipment' }}
                                    </dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                        <div class="flex items-center">
                                            <svg class="mr-2 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                @if($booking['type'] === 'room')
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                                @else
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                                @endif
                                            </svg>
                                            <span>{{ $booking['resource_name'] ?? $booking['name'] }}</span>
                                            @if(isset($booking['quantity']))
                                            <span class="ml-2 px-2 py-1 text-xs bg-gray-100 text-gray-600 rounded">
                                                Qty: {{ $booking['quantity'] }}
                                            </span>
                                            @endif
                                        </div>
                                    </dd>
                                </div>

                                <!-- Purpose -->
                                <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500">
                                        Purpose
                                    </dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                        {{ $booking['purpose'] ?? 'Not specified' }}
                                    </dd>
                                </div>

                                <!-- User Details -->
                                <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500">
                                        Booked By
                                    </dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center">
                                                <span class="text-sm font-medium text-gray-600">
                                                    {{ strtoupper(substr($booking['user']['name'] ?? 'U', 0, 1)) }}
                                                </span>
                                            </div>
                                            <div class="ml-3">
                                                <p class="text-sm font-medium text-gray-900">{{ $booking['user']['name'] ?? 'Unknown User' }}</p>
                                                <p class="text-sm text-gray-500">{{ $booking['user']['email'] ?? '' }}</p>
                                            </div>
                                        </div>
                                    </dd>
                                </div>

                                <!-- Additional Notes -->
                                @if(isset($booking['notes']) && $booking['notes'])
                                <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500">
                                        Additional Notes
                                    </dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                        <div class="bg-gray-50 p-3 rounded-md">
                                            {{ $booking['notes'] }}
                                        </div>
                                    </dd>
                                </div>
                                @endif

                                <!-- Timestamps -->
                                <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500">
                                        Timeline
                                    </dt>
                                    <dd class="mt-1 text-sm text-gray-600 sm:mt-0 sm:col-span-2 space-y-1">
                                        <div>Created: {{ \Carbon\Carbon::parse($booking['created_at'])->format('M j, Y g:i A') }}</div>
                                        @if(isset($booking['updated_at']) && $booking['updated_at'] != $booking['created_at'])
                                        <div>Last Updated: {{ \Carbon\Carbon::parse($booking['updated_at'])->format('M j, Y g:i A') }}</div>
                                        @endif
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <!-- Additional Actions -->
                    @if($booking['status'] === 'confirmed')
                    <div class="mt-6 bg-white shadow sm:rounded-lg">
                        <div class="px-4 py-5 sm:p-6">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Booking Actions</h3>
                            <div class="mt-5 flex flex-wrap gap-3">
                                @if(auth()->user()->can('checkin', $booking))
                                <form action="{{ route('booking.checkin', $booking['id']) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" 
                                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                        <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Check In
                                    </button>
                                </form>
                                @endif

                                @if(auth()->user()->can('checkout', $booking))
                                <form action="{{ route('booking.checkout', $booking['id']) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" 
                                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                        </svg>
                                        Check Out
                                    </button>
                                </form>
                                @endif

                                <!-- Download/Print Button -->
                                <a href="{{ route('booking.print', $booking['id']) }}" 
                                   target="_blank"
                                   class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                                    </svg>
                                    Print/Download
                                </a>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Right Column: Additional Info -->
                <div class="lg:col-span-1 space-y-6">
                    <!-- Quick Stats -->
                    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">
                                Quick Info
                            </h3>
                        </div>
                        <div class="px-4 py-5 sm:p-6">
                            <dl class="space-y-4">
                                <div class="flex items-center justify-between">
                                    <dt class="text-sm font-medium text-gray-500">Duration</dt>
                                    <dd class="text-sm font-semibold text-gray-900">
                                        @if(isset($booking['start_time']) && isset($booking['end_time']))
                                            @php
                                                $start = \Carbon\Carbon::parse($booking['start_time']);
                                                $end = \Carbon\Carbon::parse($booking['end_time']);
                                                $hours = $start->diffInHours($end);
                                                $minutes = $start->diffInMinutes($end) % 60;
                                            @endphp
                                            {{ $hours }}h {{ $minutes > 0 ? $minutes . 'm' : '' }}
                                        @else
                                            All day
                                        @endif
                                    </dd>
                                </div>
                                <div class="flex items-center justify-between">
                                    <dt class="text-sm font-medium text-gray-500">Days Remaining</dt>
                                    <dd class="text-sm font-semibold text-gray-900">
                                        @php
                                            $daysLeft = \Carbon\Carbon::parse($booking['date'])->diffInDays(now(), false) * -1;
                                        @endphp
                                        @if($daysLeft > 0)
                                            {{ $daysLeft }} days
                                        @elseif($daysLeft === 0)
                                            Today
                                        @else
                                            Past
                                        @endif
                                    </dd>
                                </div>
                                @if(isset($booking['capacity']))
                                <div class="flex items-center justify-between">
                                    <dt class="text-sm font-medium text-gray-500">Capacity</dt>
                                    <dd class="text-sm font-semibold text-gray-900">
                                        {{ $booking['capacity'] }} people
                                    </dd>
                                </div>
                                @endif
                            </dl>
                        </div>
                    </div>

                    <!-- Contact Information -->
                    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">
                                Contact Info
                            </h3>
                        </div>
                        <div class="px-4 py-5 sm:p-6">
                            <div class="space-y-3">
                                <div class="flex items-start">
                                    <svg class="h-5 w-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                                    </svg>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">Email</p>
                                        <p class="text-sm text-gray-500">{{ $booking['user']['email'] ?? 'Not available' }}</p>
                                    </div>
                                </div>
                                @if(isset($booking['contact_number']))
                                <div class="flex items-start">
                                    <svg class="h-5 w-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                    </svg>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">Phone</p>
                                        <p class="text-sm text-gray-500">{{ $booking['contact_number'] }}</p>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Related Bookings -->
                    @if(isset($relatedBookings) && count($relatedBookings) > 0)
                    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">
                                Related Bookings
                            </h3>
                        </div>
                        <div class="px-4 py-5 sm:p-6">
                            <ul class="space-y-3">
                                @foreach($relatedBookings as $related)
                                <li class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $related['name'] }}</p>
                                        <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($related['date'])->format('M j, Y') }}</p>
                                    </div>
                                    <a href="{{ route('booking.show', $related['id']) }}" 
                                       class="text-sm text-blue-600 hover:text-blue-900">
                                        View
                                    </a>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Back Button -->
            <div class="mt-8">
                <a href="{{ url()->previous() }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back
                </a>
            </div>
        </div>
    </div>
</div>
@endsection