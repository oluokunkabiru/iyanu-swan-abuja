<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\JsonResponse;

class SiteSettingController extends Controller
{
    public function show(): JsonResponse
    {
        $settings = SiteSetting::current();

        return response()->json([
            'chapterName' => $settings->chapter_name,
            'shortName' => $settings->short_name ?? $settings->chapter_name,
            'parentBody' => $settings->parent_body ?? 'Institute of Chartered Accountants of Nigeria',
            'tagline' => $settings->tagline ?? '',
            'vision' => $settings->vision ?? '',
            'mission' => $settings->mission_items ?? array_filter([$settings->mission]),
            'aims' => $settings->aims ?? '',
            'address' => $settings->address ?? '',
            'phone' => $settings->phone ?? '',
            'email' => $settings->email ?? '',
            'socials' => $settings->social_links ?? [],
            'subscriptionFee' => $settings->membership_subscription_fee,
            'welfareFee' => $settings->membership_welfare_fee,
            'logoUrl' => $settings->logo_url,
            'chairpersonWelcome' => [
                'heading' => $settings->chairperson_heading ?? '',
                'paragraphs' => $settings->chairperson_message ?? [],
            ],
            'chapterStats' => $settings->chapter_stats ?? [],
            'registrationSteps' => $settings->registration_steps ?? [],
            'memberBenefits' => $settings->member_benefits ?? [],
            'aimsAndObjectives' => $settings->aims_objectives ?? [],
        ]);
    }
}
