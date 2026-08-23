<?php

use App\Events\PostLiked;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

it('implements ShouldBroadcast interface', function () {
    $event = new PostLiked(42);

    expect($event)->toBeInstanceOf(ShouldBroadcast::class);
});

it('broadcasts on the correct post channel', function () {
    $event = new PostLiked(15);
    $channels = $event->broadcastOn();

    expect($channels)->toHaveCount(1);
    expect($channels[0])->toBeInstanceOf(Channel::class);
    expect($channels[0]->name)->toBe('posts.15');
});

it('broadcastswith the alias PostLiked', function () {
    $event = new PostLiked(15);

    expect($event->broadcastAs())->toBe('PostLiked');
});

it('holds the correct postId property', function () {
    $event = new PostLiked(99);

    expect($event->postId)->toBe(99);
});
