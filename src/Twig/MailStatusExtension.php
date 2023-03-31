<?php

namespace App\Twig;

use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use App\Entity\Participant;

class MailStatusExtension extends AbstractExtension
{
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('mailstatus', [$this, 'mailstatusFilter']),
        ];
    }

    public function mailstatusFilter(Participant $participant): string
    {
        if ($participant->getParty()->getCreated()) {
            switch (true) {
                case $participant->getViewdate() != null:
                    $status = $this->translator->trans('mail_status_extension.viewed');
                    $icon = 'fa-check';
                    $type = 'success';

                    break;
                case $participant->getOpenEmailDate() != null:
                    $status = $this->translator->trans('mail_status_extension.opened');
                    $icon = 'fa-eye';
                    $type = 'warning';

                    break;
                case $participant->getEmailDidBounce():
                    $status = $this->translator->trans('mail_status_extension.bounced');
                    $icon = 'fa-exclamation-triangle';
                    $type = 'danger';

                    break;
                default:
                    $status = $this->translator->trans('mail_status_extension.unknown');
                    $icon = 'fa-question';
                    $type = 'muted';
            }
        } else {
            $status = $this->translator->trans('mail_status_extension.not_started');
            $icon = 'fa-info';
            $type = 'muted';
        }

        return '<span class="text-'.$type.'"><i class="fa '.$icon.'" aria-hidden="true"></i> '.$status.'</span>';
    }
}
