<?php declare(strict_types = 1);
namespace PharIo\Phive;

class ResetCommand implements Cli\Command {

    /** @var ResetCommandConfig */
    private $config;

    /** @var PharRegistry */
    private $pharRegistry;

    /** @var Environment */
    private $environment;

    /** @var PharInstaller */
    private $pharInstaller;

    public function __construct(
        ResetCommandConfig $config,
        PharRegistry $pharRegistry,
        Environment $environment,
        PharInstaller $pharInstaller
    ) {
        $this->config        = $config;
        $this->pharRegistry  = $pharRegistry;
        $this->environment   = $environment;
        $this->pharInstaller = $pharInstaller;
    }

    public function execute(): void {
        $aliasFilter = [];

        if ($this->config->hasAliases()) {
            $aliasFilter = $this->config->getAliases();
        }

        foreach ($this->pharRegistry->getUsedPharsByDestination($this->environment->getWorkingDirectory()) as $phar) {
            if (!empty($aliasFilter) && !\in_array($phar->getName(), $aliasFilter)) {
                continue;
            }
            $this->pharInstaller->install($phar->getFile(), $this->environment->getWorkingDirectory()->file($phar->getName()), false);
        }
    }
}
