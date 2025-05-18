<?php

declare(strict_types=1);

namespace App\Tests\Unit\Helpers;

use Macroactive\Core\Common\Domain\Model\Locale;
use Macroactive\Core\Common\Domain\Model\OId;
use Macroactive\Notifications\Application\Repositories\ContentPartsRepositoryInterface;
use Macroactive\Notifications\Domain\Model\ContentPart;
use Macroactive\Notifications\Domain\Model\ContentPartTag;
use Macroactive\Notifications\Tests\Unit\Mocks\TestNotificationsId;

class InMemoryContentPartsRepository implements ContentPartsRepositoryInterface
{
    /** @var ContentPart[] */
    public array $storage = [];

    /**
     * @param ContentPart[]|null $storage
     */
    public function __construct(?array $storage = null)
    {
        if ($storage !== null) {
            $this->storage = $storage;

            return;
        }

        $this->storage = [
            new ContentPart(
                OId::next(),
                OId::fromString(TestNotificationsId::COMMUNITIES__NEW_POST_OID),
                ContentPartTag::PUSH_TITLE,
                new Locale('en'),
                '{{Name}} just dropped a new post!',
            ),
            new ContentPart(
                OId::next(),
                OId::fromString(TestNotificationsId::COMMUNITIES__NEW_POST_OID),
                ContentPartTag::PUSH_TITLE,
                new Locale('en'),
                'Customized notification that {{Name}} just dropped a new post!',
                OId::fromString('7bc55b76-3c6b-4644-9251-9c093b2c5a16'),
            ),
            new ContentPart(
                OId::next(),
                OId::fromString(TestNotificationsId::COMMUNITIES__NEW_POST_OID),
                ContentPartTag::PUSH_BODY,
                new Locale('en'),
                '{{PostContent}}'
            ),
            new ContentPart(
                OId::next(),
                OId::fromString(TestNotificationsId::NOTIFICATION_WITH_ENABLED_PUSH_TRANSPORT_OID),
                ContentPartTag::PUSH_TITLE,
                new Locale('en'),
                'Great news! Your post is approved and published!',
            ),
            new ContentPart(
                OId::next(),
                OId::fromString(TestNotificationsId::NOTIFICATION_WITH_ENABLED_PUSH_TRANSPORT_OID),
                ContentPartTag::PUSH_BODY,
                new Locale('en'),
                '[Post content up-to 80 characters]',
            ),
            new ContentPart(
                OId::next(),
                OId::fromString(TestNotificationsId::NOTIFICATION_WITH_ENABLED_PUSH_TRANSPORT_OID),
                ContentPartTag::PUSH_APPURL,
                new Locale('en'),
                '/goto?route=posts&postId={{PostId}}',
            ),
            new ContentPart(
                OId::next(),
                OId::next(),
                ContentPartTag::PUSH_TITLE,
                new Locale('en'),
                'We’re sorry, but your post didn’t meet the approval criteria.'
            ),
            new ContentPart(
                OId::next(),
                OId::next(),
                ContentPartTag::PUSH_BODY,
                new Locale('en'),
                '[Post content up-to 80 characters]'
            ),
            new ContentPart(
                OId::next(),
                OId::next(),
                ContentPartTag::PUSH_TITLE,
                new Locale('en'),
                "[Creator/member's first name or nickname if it exists] mentioned you in a post!"
            ),
            new ContentPart(
                OId::next(),
                OId::next(),
                ContentPartTag::PUSH_BODY,
                new Locale('en'),
                '[Post content up-to 80 characters]'
            ),
            new ContentPart(
                OId::next(),
                OId::next(),
                ContentPartTag::PUSH_TITLE,
                new Locale('en'),
                "[Creator/member's first name or nickname if it exists] mentioned you in a comment!"
            ),
            new ContentPart(
                OId::next(),
                OId::next(),
                ContentPartTag::PUSH_BODY,
                new Locale('en'),
                '[Comment content up-to 80 characters]'
            ),
            new ContentPart(
                OId::next(),
                OId::next(),
                ContentPartTag::PUSH_TITLE,
                new Locale('en'),
                "[Creator/member's first name or nickname if it exists] commented on your post!"
            ),
            new ContentPart(
                OId::next(),
                OId::next(),
                ContentPartTag::PUSH_BODY,
                new Locale('en'),
                '[Comment content up-to 80 characters]'
            ),
            new ContentPart(
                OId::next(),
                OId::next(),
                ContentPartTag::PUSH_TITLE,
                new Locale('en'),
                "[Creator/member's first name or nickname if it exists] liked your post!"
            ),
            new ContentPart(
                OId::next(),
                OId::next(),
                ContentPartTag::PUSH_BODY,
                new Locale('en'),
                '[Post content up-to 80 characters]'
            ),
        ];
    }

    /**
     * @param ContentPartTag[] $tags
     *
     * @return ContentPart[]
     */
    public function fetchForCreatorIncludingDefaults(
        OId $notificationInternalId,
        OId $creatorId,
        Locale $locale,
        ?array $tags = null,
    ): array {
        return array_filter(
            $this->storage,
            static fn (ContentPart $contentPart
            ): bool => $contentPart->getNotificationInternalId()->isEqual($notificationInternalId)
                && ($tags === null || in_array($contentPart->getTag(), $tags, true))
                && $contentPart->getLocale()->isEqual($locale)
                && (
                    $creatorId === null
                    || $contentPart->getCreatorId() === null
                    || $contentPart->getCreatorId()->isEqual($creatorId)
                )
        );
    }

    /**
     * @return ContentPart[]
     */
    public function fetchDefaultsForNotification(OId $notificationInternalId, Locale $locale): array
    {
        return array_filter(
            $this->storage,
            static fn (ContentPart $contentPart
            ): bool => $contentPart->getNotificationInternalId()->isEqual($notificationInternalId)
                && $contentPart->getLocale()->isEqual($locale)
                && $contentPart->getCreatorId() === null
        );
    }

    public function add(ContentPart $contentPart): void
    {
        if (!array_filter($this->storage, static fn (ContentPart $storedContentPart): bool => $storedContentPart->getId()->isEqual($contentPart->getId()))) {
            $this->storage[] = $contentPart;
        }
    }

    public function remove(ContentPart $contentPart): void
    {
        $this->storage = array_filter(
            $this->storage,
            static fn (ContentPart $storedContentPart
            ): bool => !$storedContentPart->getId()->isEqual($contentPart->getId())
        );
    }

    public function fetchForNotificationByCreators(OId $notificationInternalId, array $creatorIds): array
    {
        $result = [];

        foreach ($this->storage as $contentPart) {
            // Check if the notification ID matches
            if (!$contentPart->getNotificationInternalId()->isEqual($notificationInternalId)) {
                continue;
            }

            // Include content parts with a null creator ID
            if ($contentPart->getCreatorId() === null) {
                $result[] = $contentPart;

                continue;
            }

            // Include content parts with matching creator IDs
            foreach ($creatorIds as $creatorId) {
                if ($contentPart->getCreatorId()->isEqual($creatorId)) {
                    $result[] = $contentPart;

                    break;
                }
            }
        }

        return $result;
    }
}
