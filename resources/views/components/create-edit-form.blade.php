<input type="file"
    class="w-full border border-gray-300 dark:border-gray-700 bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-white block focus:outline-none rounded-xl"
    name="image" id="file_input">
<p class="mt-2 text-sm text-gray-500 dark:text-gray-400" id="file_input_help">{{ __('PNG, JPG, or GIF') }}</p>
<textarea name="description" rows="5" id="" cols="30" rows="10"
    class="mt-2 w-full text-gray-900 dark:text-white border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 rounded-xl p-3 placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-indigo-500"
    placeholder="{{ __('Write a decription...') }}">{{ $post->description ?? '' }}</textarea>
