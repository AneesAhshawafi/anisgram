 <input type="file" class="w-full border border-gray-200 bg-gray-500 block focus:outline-none rounded-xl" name="image"
     id="file_input">
 <p class="mt-2 text-sm text-gray-500 dark:test-gray-300" id="file_input_help">PNG, JPG, or GIF</p>
 <textarea name="description" rows="5" id="" cols="30" rows="10"
     class="mt-2 w-full text-white border border-gray-200 bg-gray-900 rounded-xl"
     placeholder="{{ __('Write a decription...') }}">{{ $post->description ?? '' }}</textarea>
