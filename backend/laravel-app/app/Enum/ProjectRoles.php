<?php

namespace App\Enum;

enum ProjectRoles: string
{
    case Owner = 'owner';
    case Member = 'member';
    case Viewer = 'viewer';
}
