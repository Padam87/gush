<?php

/*
 * This file is part of Gush.
 *
 * (c) Luis Cordova <cordoval@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Gush\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PullRequestMergeCommand extends BaseCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('pull-request:merge')
            ->setDescription('Pull request command')
            ->addArgument('prNumber', InputArgument::REQUIRED, 'Pull Request number')
            ->addArgument('org', InputArgument::OPTIONAL, 'Name of the GitHub organization', $this->getVendorName())
            ->addArgument('repo', InputArgument::OPTIONAL, 'Name of the GitHub repository', $this->getRepoName())
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $org = $input->getArgument('org');
        $repo = $input->getArgument('repo');
        $prNumber = $input->getArgument('prNumber');

        $client = $this->getGithubClient();

        $pr = $client->api('pull_request')->show($org, $repo, $prNumber);
        $commits = $client->api('pull_request')->commits($org, $repo, $prNumber);

        $message = $this->render(
            'merge_message.twig',
            [
                'baseBranch' => $pr['base']['label'],
                'prTitle' => $pr['title'],
                'prBody' => $pr['body'],
                'commits' => $commits
            ]
        );

        $merge = $client->api('pull_request')->merge($org, $repo, $prNumber, $message);

        if ($merge['merged']) {
            $output->writeln($merge['message']);
        } else {
            $output->writeln('There was a problem merging: '.$merge['message']);
        }

        return self::COMMAND_SUCCESS;
    }
}
