 <div class="mt-5">
     <h3 class="text-gray-600 dark:text-gray-400 font-bold">
         {{ __('Suggestions For You') }}
     </h3>
     <ul>
         @foreach ($this->suggested_users as $suggested_user)
             <li class="mt-3">
                 <div class="flex flex-row text-sm">
                     <div class="rtl:ml-5 ltr:mr-5">

                         <a href="/{{ $suggested_user->username }}">
                             <img src="{{ $suggested_user->image }}" alt="{{ $suggested_user->username }}"
                                 class="border border-gray-300 dark:border-gray-700 rounded-full  h-12 w-12 aspect-square object-cover">
                         </a>
                     </div>
                     <div class="flex flex-col grow">
                         <a href="/{{ $suggested_user->username }}"
                             class="font-bold text-gray-900 dark:text-white">{{ $suggested_user->username }}
                             @if (auth()->user()->isFollower($suggested_user))
                                 <span class="text-gray-500 dark:text-gray-400 text-sm font-normal">
                                     ({{ __('follower') }})</span>
                             @endif
                         </a>
                         <div class="text-gray-500 dark:text-gray-400 text-sm">{{ $suggested_user->name }}</div>
                     </div>
                     <livewire:posts.follow-button :user_id="$suggested_user->id" />
                 </div>
             </li>
         @endforeach
     </ul>
 </div>
