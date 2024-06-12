
    <x-list-layout>
        <div class="flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100 dark:bg-gray-900">
            {{ $paginator->links() }}<br><br><br>
        </div>
        <div class="flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100 dark:bg-gray-900">
            <x-primary-button class="ms-4">
                <a href="/register_page">Add new</a>
            </x-primary-button>
        </div>



    @foreach ($users as $user)

        <div class="flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100 dark:bg-gray-900">
            <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white dark:bg-gray-800 shadow-md overflow-hidden sm:rounded-lg">
                <div><strong>ID:</strong>    {{ $user['id'] }}</div>
                <div><strong>Name:</strong>  {{ $user['name'] }}</div>
                <div><strong>Email:</strong>{{ $user['email'] }}</div>
                <div><strong>Position:</strong>{{ $user['position'] }}</div>
                <div><strong>Phone:</strong> {{ $user['phone'] }}</div>
                <div><img src="{{$user['photo']}}" width="70" height="70"></div>
            </div>
        </div>
    @endforeach
        <div class="flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100 dark:bg-gray-900">{{ $paginator->links() }}</div>
    </x-list-layout>

