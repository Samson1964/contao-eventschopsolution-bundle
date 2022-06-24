<?php

namespace Schachbulle\ContaoEventschopsolutionBundle\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Schachbulle\ContaoEventschopsolutionBundle\ContaoEventschopsolutionBundle;

class Plugin implements BundlePluginInterface
{
	/**
	 * {@inheritdoc}
	 */
	public function getBundles(ParserInterface $parser)
	{
		return [
			BundleConfig::create(ContaoEventschopsolutionBundle::class)
				->setLoadAfter([ContaoCoreBundle::class], [ContaoCalendarBundle::class]),
		];
	}
}
