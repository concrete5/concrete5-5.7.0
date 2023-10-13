<?php
namespace Concrete\Core\SiteInformation;

use Concrete\Core\SiteInformation\Question\BuildQuestion;
use Concrete\Core\SiteInformation\Question\OrganizationQuestion;
use Concrete\Core\SiteInformation\Question\RoleQuestion;

class SiteInformationSurvey extends AbstractSurvey
{

    public function getSaverNamespace(): string
    {
        return 'general';
    }

    public function getQuestions(): array
    {
        return [
            new BuildQuestion(),
            new RoleQuestion(),
            new OrganizationQuestion(),
        ];
    }

}
