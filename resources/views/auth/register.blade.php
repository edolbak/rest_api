<x-guest-layout>


    <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')"  autofocus autocomplete="name" />
{{--            <x-input-error :messages="$errors->get('name')" class="mt-2" />--}}
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="text" name="email" :value="old('email')"  autocomplete="username" />
{{--            <x-input-error :messages="$errors->get('email')" class="mt-2" />--}}
        </div>

        <!-- Phone -->
        <div class="mt-4">
            <x-input-label for="phone" :value="__('Phone')" />
            <x-text-input id="phone" class="block mt-1 w-full" type="text" name="phone" :value="old('phone')"  />
{{--            <x-input-error :messages="$errors->get('email')" class="mt-2" />--}}
        </div>

        <!-- Position -->
        <div class="mt-4">
            <x-input-label for="position_id" :value="__('Position')" />

            <select name="position_id">
                @foreach($positions as $position)
                    <option value="{{$position['id']}}" >{{$position['name']}}</option>
                @endforeach
            </select>
        </div>
        <br>

        <div>
            <x-input-label for="photo" :value="__('Photo')" />
            <x-text-input  type="file" class="form-control" name="photo" />
        </div>

        <!-- Password -->
{{--        <div class="mt-4">--}}
{{--            <x-input-label for="password" :value="__('Password')" />--}}

{{--            <x-text-input id="password" class="block mt-1 w-full"--}}
{{--                            type="password"--}}
{{--                            name="password"--}}
{{--                            required autocomplete="new-password" />--}}

{{--            <x-input-error :messages="$errors->get('password')" class="mt-2" />--}}
{{--        </div>--}}

        <!-- Confirm Password -->
{{--        <div class="mt-4">--}}
{{--            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />--}}

{{--            <x-text-input id="password_confirmation" class="block mt-1 w-full"--}}
{{--                            type="password"--}}
{{--                            name="password_confirmation" required autocomplete="new-password" />--}}

{{--            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />--}}
{{--        </div>--}}

        <div class="flex items-center justify-end mt-4">
{{--            <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('login') }}">--}}
{{--                {{ __('Already registered?') }}--}}
{{--            </a>--}}

            <x-primary-button class="ms-4">
                {{ __("Add user") }}
            </x-primary-button>
        </div>
    </form>

    <br><br><br>
    <div class="flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100 dark:bg-gray-900">
        <x-primary-button class="ms-4">
            <a href="/">To list</a>
        </x-primary-button>
    </div>
</x-guest-layout>
