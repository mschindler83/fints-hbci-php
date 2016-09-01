<?php

namespace Fhp\Message;

use Fhp\DataElementGroups\SecurityProfile;
use Fhp\Segment\AbstractSegment;
use Fhp\Segment\HNHBS;
use Fhp\Segment\HNSHA;
use Fhp\Segment\HNSHK;
use Fhp\Segment\HNVSD;
use Fhp\Segment\HNVSK;
use Fhp\Segment\SegmentInterface;

interface EncryptedSegment {

    public function getEncryptedSegments();
}