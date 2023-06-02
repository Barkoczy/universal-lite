<?php
namespace App\Kernel\Extensions\Twig;

use Symfony\Component\Yaml\Yaml;
use Twig\TwigFilter;
use Twig\Extension\AbstractExtension;
use App\Kernel\Exceptions\CannotReadFileFromFilesource;
use App\Kernel\Filesystem\Folder;

class Whitespace extends AbstractExtension
{
	/** @var array **/
	protected $patterns = [];
	
	/** @var array **/
	protected $replace = [];

	public function __construct()
	{
		// @validate
    if (!is_file($this->filepath()))
      throw new CannotReadFileFromFilesource('Cannot read twig extension whitespace file due to insufficient permissions');

    // @data
		$data = Yaml::parseFile($this->filepath());

		// @parse
		if (is_array($data) && 0 < count($data)) {
			foreach($data as $item) {
				if(isset($item[0]) && isset($item[1])) {
					$this->patterns[] = $item[0];
					$this->replace[]  = $item[1];
				}
			}
		}
	}

	/**
	* @return array
	*/
	public function getFilters(): array
	{
		return [
			new TwigFilter('whitespace', [$this, 'whitespace']),
		];
	}

	/**
	* @param string $txt
	* @return string
	*/
	public function whitespace(string $txt = ''): string
	{
		return str_replace($this->patterns, $this->replace, $txt);
	}

	/**
   * Return config file path
   *
   * @return string
   */
  private function filepath(): string
  {
    return Folder::getConfigPath().'/whitespace.yml';
  }
}