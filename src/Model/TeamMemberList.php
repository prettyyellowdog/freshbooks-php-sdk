<?php

declare(strict_types=1);

namespace amcintosh\FreshBooks\Model;

use Spatie\DataTransferObject\Attributes\CastWith;
use Spatie\DataTransferObject\Attributes\MapFrom;
use Spatie\DataTransferObject\Casters\ArrayCaster;
use amcintosh\FreshBooks\Model\ProjectLikeList;
use amcintosh\FreshBooks\Model\TeamMember;

/**
 * List of all team members
 *
 * @package amcintosh\FreshBooks\Model
 * @link https://www.freshbooks.com/api/team-member
 */
class TeamMemberList extends ProjectLikeList
{
    #[CastWith(ArrayCaster::class, itemType: TeamMember::class)]
    #[MapFrom('result')]
    public array $team;
}
