<?php

declare(strict_types=1);

namespace amcintosh\FreshBooks\Model;

use DateTimeImmutable;
use Spatie\DataTransferObject\Attributes\CastWith;
use Spatie\DataTransferObject\Attributes\MapFrom;
use Spatie\DataTransferObject\Attributes\MapTo;
use Spatie\DataTransferObject\Caster;
use Spatie\DataTransferObject\DataTransferObject;
use amcintosh\FreshBooks\Model\Caster\ISODateTimeImmutableCaster;

/**
 * The team members resource is how you interact with employees/users of your FreshBooks business.
 * This supersedes the Staff resource which was used primarily by FreshBooks Classic.
 *
 * @package amcintosh\FreshBooks\Model
 * @link https://www.freshbooks.com/api/team-members
 */
class TeamMember extends DataTransferObject
{
    /**
     * @var string Unique id of the team member
     */
    public ?string $uuid;

    /**
     * @var string Team members's first name.
     */
    #[MapFrom('first_name')]
    public ?string $firstName;

    /**
     * @var string Team member's middle name.
     */
    #[MapFrom('middle_name')]
    public ?string $middleName;

    /**
     * @var string Team member's last name.
     */
    #[MapFrom('last_name')]
    public ?string $lastName;

    /**
     * @var string Team member's email.
     */
    public ?string $email;

    /**
     * @var string Team member's job title.
     */
    #[MapFrom('job_title')]
    public ?string $jobTitle;

    /**
     * @var string Team member's street address.
     */
    #[MapFrom('street_1')]
    public ?string $street1;

    /**
     * @var string Team member's street address (line 2).
     */
    #[MapFrom('street_2')]
    public ?string $street2;

    /**
     * @var string Team member's city.
     */
    public ?string $city;

    /**
     * @var string Team member's province / state.
     */
    public ?string $province;

    /**
     * @var string Team member's country.
     */
    public ?string $country;

    /**
     * @var string Team member's postal code.
     */
    #[MapFrom('postal_code')]
    public ?string $postalCode;

    /**
     * @var string Team member's phone number.
     */
    #[MapFrom('phone_number')]
    public ?string $phoneNumber;

    /**
     * @var string The id of this business
     */
    #[MapFrom('business_id')]
    public ?int $businessId;

    /**
     * @var string Team member’s role in the business
     */
    #[MapFrom('business_role_name')]
    public ?string $businessRoleName;

    /**
     * @var string Whether the member is active or not
     */
    public ?bool $active;

    /**
     * @var string The identity_id of the team member if they have a FreshBooks account
     */
    #[MapFrom('identity_id')]
    public ?int $identityId;

    /**
     * @var DateTimeImmutable The date/time the team member accepted their invitation
     */
    #[CastWith(ISODateTimeImmutableCaster::class)]
    #[MapFrom('invitation_date_accepted')]
    public ?DateTimeImmutable $invitationDateAccepted;

    /**
     * @var DateTimeImmutable The date/time the team member was created
     */
    #[CastWith(ISODateTimeImmutableCaster::class)]
    #[MapFrom('created_at')]
    public ?DateTimeImmutable $createdAt;

    /**
     * @var DateTimeImmutable The date/time the team member was last modified
     */
    #[CastWith(ISODateTimeImmutableCaster::class)]
    #[MapFrom('updated_at')]
    public ?DateTimeImmutable $updatedAt;
}
