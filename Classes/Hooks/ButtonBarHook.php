<?php
declare(strict_types=1);

namespace GeorgRinger\PageSpeed\Hooks;

use GeorgRinger\PageSpeed\Domain\Model\Response;
use GeorgRinger\PageSpeed\Domain\Repository\PageSpeedRepository;
use GeorgRinger\PageSpeed\Service\UrlService;
use TYPO3\CMS\Backend\Template\Components\ButtonBar;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ButtonBarHook
{

    /** @var int */
    protected $pageId;

    public function __construct()
    {
        $pageId = GeneralUtility::_GET('id');
        if (isset($pageId)) {
            $this->pageId = (int)$pageId;
        }
    }

    /**
     * @param array $params
     * @param ButtonBar $buttonBar
     * @return array
     */
    public function getButtons(array $params, ButtonBar $buttonBar): array
    {
        $buttons = $params['buttons'];
        if (!$this->isValid()) {
            return $buttons;
        }

        $iconFactory = GeneralUtility::makeInstance(IconFactory::class);

        $pageSpeedRepository = GeneralUtility::makeInstance(PageSpeedRepository::class);
        $result = $pageSpeedRepository->findByIdentifier(UrlService::getFullUrl($this->pageId), false);

        foreach ($result as $strategy => $singleResult) {
            if ($singleResult) {
                $usability = $singleResult->getScoreUsability();
                $speed = $singleResult->getScoreSpeed();

                $value = $speed . ($usability ? '/' . $usability : '');
                $button = $buttonBar->makeLinkButton();
                $button->setIcon($iconFactory->getIcon('ext-pagespeed-' . $strategy, Icon::SIZE_SMALL));
                $button->setTitle($value);
                $button->setShowLabelText(true);
                $button->setClasses('page-speed-' . $this->getColorOfScore($speed, $usability));
                $button->setOnClick('$("#ga-dashboard").toggle();return false;');
                $buttons[ButtonBar::BUTTON_POSITION_RIGHT][2][] = $button;
            }
        }

        return $buttons;
    }

    protected function getColorOfScore(int $speed, int $usability)
    {
        $code = 'none';
        if ($speed === 0 && $usability === 0) {
            return $code;
        }

        $values[$speed] = $speed;
        $values[$usability] = $usability;
        unset($values[0]);

        $number = min($values);

        if ($number === 0) {
            return 'none';
        } elseif ($number <= Response::INDICATOR_LOW) {
            return 'danger';
        } elseif ($number > Response::INDICATOR_HIGH) {
            return 'ok';
        } else {
            return 'warning';
        }
    }

    protected function isValid(): bool
    {
        $route = GeneralUtility::_GET('route');
        return $route === '/web/layout/' && $this->pageId !== null;
    }
}
