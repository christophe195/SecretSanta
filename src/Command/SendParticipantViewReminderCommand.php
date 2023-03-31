<?php

namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use App\Mailer\MailerService;
use App\Query\ParticipantMailQuery;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SendParticipantViewReminderCommand extends Command
{
    private EntityManagerInterface $em;
    private ParticipantMailQuery $participantMailQuery;
    private MailerService $mailerService;

    public function __construct(
        EntityManagerInterface $em,
        ParticipantMailQuery $participantMailQuery,
        MailerService $mailerService
    ) {
        $this->em = $em;
        $this->participantMailQuery = $participantMailQuery;
        $this->mailerService = $mailerService;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('app:sendParticipantViewReminderMails')
            ->setDescription('Send reminder to participants to confirm their presence at the party');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $needsViewReminder = $this->participantMailQuery->findAllToRemindToViewParticipant();
        $timeNow = new \DateTime();

        try {
            foreach ($needsViewReminder as $participant) {
                $this->mailerService->sendParticipantViewReminderMail($participant);

                $participant->setViewReminderSentTime($timeNow);
                $this->em->persist($participant);
            }

            $this->em->flush();
        } catch (\Exception $e) {
            throw $e;
        } finally {
            $this->em->flush();
        }

        return 0;
    }
}
