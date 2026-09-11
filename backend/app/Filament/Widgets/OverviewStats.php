<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\ContactMessages\ContactMessageResource;
use App\Filament\Resources\Events\EventResource;
use App\Filament\Resources\Users\UserResource;
use App\Models\ContactMessage;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\MemberProfile;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class OverviewStats extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $activeMembers = MemberProfile::where('membership_status', 'active')->count();
        $pendingMembers = MemberProfile::where('membership_status', 'pending')->count();

        $upcomingEvents = Event::where('status', 'published')
            ->where('starts_at', '>=', now())
            ->count();

        $confirmedRegistrations = EventRegistration::where('payment_status', 'confirmed')->count();
        $pendingRegistrations = EventRegistration::where('payment_status', 'pending')->count();

        $revenue = EventRegistration::where('payment_status', 'confirmed')->sum('amount');

        $unreadMessages = ContactMessage::where('is_read', false)->count();

        $memberTrend = collect(range(6, 0))
            ->map(fn ($daysAgo) => MemberProfile::whereDate('created_at', now()->subDays($daysAgo)->toDateString())->count())
            ->all();

        $revenueTrend = collect(range(6, 0))
            ->map(fn ($daysAgo) => (int) EventRegistration::where('payment_status', 'confirmed')
                ->whereDate('created_at', now()->subDays($daysAgo)->toDateString())
                ->sum('amount'))
            ->all();

        return [
            Stat::make('Active members', $activeMembers)
                ->description("{$pendingMembers} pending approval")
                ->descriptionIcon('heroicon-m-user-plus')
                ->color($pendingMembers > 0 ? 'warning' : 'success')
                ->chart($memberTrend)
                ->url(UserResource::getUrl('index')),

            Stat::make('Revenue collected', '₦'.number_format($revenue))
                ->description('From confirmed registrations')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success')
                ->chart($revenueTrend)
                ->url(EventResource::getUrl('index')),

            Stat::make('Event registrations', $confirmedRegistrations)
                ->description("{$pendingRegistrations} awaiting confirmation")
                ->descriptionIcon('heroicon-m-clock')
                ->color($pendingRegistrations > 0 ? 'warning' : 'success')
                ->url(EventResource::getUrl('index')),

            Stat::make('Upcoming events', $upcomingEvents)
                ->description('Published & scheduled')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('info')
                ->url(EventResource::getUrl('index')),

            Stat::make('Unread messages', $unreadMessages)
                ->description($unreadMessages > 0 ? 'Needs attention' : 'All caught up')
                ->descriptionIcon('heroicon-m-envelope')
                ->color($unreadMessages > 0 ? 'danger' : 'success')
                ->url(ContactMessageResource::getUrl('index')),
        ];
    }
}
