<?php

namespace Notification\Model;

enum NotificationStatus
{
    case SENT;
    case FAILED;
}
