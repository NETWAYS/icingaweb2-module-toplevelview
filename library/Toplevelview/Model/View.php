<?php

namespace Icinga\Module\Toplevelview\Model;

use Icinga\Application\Benchmark;
use Icinga\Exception\InvalidPropertyException;
use Icinga\Exception\NotImplementedError;
use Icinga\Exception\ProgrammingError;
use Icinga\Module\Toplevelview\Tree\TLVTree;

/**
 * View represents a single Top Level View.
 * It contains the actual Tree and metadata.
 */
class View
{
    const FORMAT_YAML = 'yml';

    /**
     * Format of this View (e.g. 'yml')
     */
    protected string $format;
    /**
     * Name if this view (also used for the filename)
     */
    protected ?string $name;
    /**
     * Contains the parsed YAML (yaml_parse)
     */
    protected ?array $raw;
    /**
     * The TLVTree object for this View
     */
    protected ?TLVTree $tree;
    /**
     * Has this View been loaded
     */
    public bool $hasBeenLoaded = false;
    /**
     * Has this View been loaded from a session
     */
    public bool $hasBeenLoadedFromSession = false;
    /**
     * Content of the configuration file
     */
    protected string $text = '';
    /**
     * SHA1 checksum of the text
     */
    protected ?string $textChecksum;

    public function __construct(string $name, string $format)
    {
        $this->name = $name;
        $this->format = $format;
    }

    public function __clone()
    {
        $this->name = null;
        $this->raw = null;
        $this->tree = null;

        $this->hasBeenLoaded = false;
        $this->hasBeenLoadedFromSession = false;
    }

    /**
     * getTree loads the Tree for this configuration
     *
     * @return TLVTree
     */
    public function getTree(): TLVTree
    {
        if ($this->tree === null) {
            $this->ensureParsed();
            $this->tree = $tree = TLVTree::fromArray($this->raw);
            $tree->setViewName($this->getName());
            $tree->setViewChecksum($this->getTextChecksum());
        }
        return $this->tree;
    }

    /**
     * ensureParsed parses the Views YAML text.
     *
     * @throws InvalidPropertyException if the YAML config cannot be parsed
     * @throws NotImplementedError if the format is unknown
     */
    protected function ensureParsed(): void
    {
        if ($this->raw === null) {
            Benchmark::measure('Begin parsing YAML document');

            $text = $this->getText();
            if ($text === '') {
                $this->raw = [];
            } elseif ($this->format == self::FORMAT_YAML) {
                $this->raw = yaml_parse($text);
                if (! is_array($this->raw)) {
                    throw new InvalidPropertyException('Could not parse YAML config!');
                }
            } else {
                throw new NotImplementedError("Unknown format '%s'", $this->format);
            }

            Benchmark::measure('Finished parsing YAML document');
        }
    }

    /**
     * getMeta returns a value from the View's metadata.
     * Metadata are root elemts in the YAML file that are not 'children'
     * @throws ProgrammingError if you try to edit children here
     */
    public function getMeta($key)
    {
        $this->ensureParsed();
        if ($key !== 'children' && array_key_exists($key, $this->raw)) {
            return $this->raw[$key];
        } else {
            return null;
        }
    }

    /**
     * setMeta sets a given key's value
     *
     * @throws ProgrammingError if you try to edit children here
     */
    public function setMeta($key, $value): View
    {
        if ($key === 'children') {
            throw new ProgrammingError('You can not edit children here!');
        }
        $this->raw[$key] = $value;
        return $this;
    }

    /**
     * getMetaData returns all YAML root elements that are not 'childen',
     * thus the View's metadata.
     */
    public function getMetaData(): array
    {
        $this->ensureParsed();
        $data = [];
        foreach ($this->raw as $key => $value) {
            if ($key !== 'children') {
                $data[$key] = $value;
            }
        }
        return $data;
    }

    /**
     * @return bool
     */
    public function hasBeenLoadedFromSession()
    {
        return $this->hasBeenLoadedFromSession;
    }

    /**
     * @return bool
     */
    public function hasBeenLoaded()
    {
        return $this->hasBeenLoaded;
    }

    /**
     * getText returns the Views text, which contains the full YAML data
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * getTextChecksum returns the textChecksum of this View
     * @return string
     */
    public function getTextChecksum(): string
    {
        if ($this->textChecksum === null) {
            $this->textChecksum = sha1($this->text);
        }
        return $this->textChecksum;
    }

    /**
     * getFormat returns the View's format
     */
    public function getFormat(): string
    {
        return $this->format;
    }

    /**
     * setText sets the text (YAML) for this View.
     * Hint: This will reset textChecksum, raw, and tree
     * @param $text string
     * @return View
     */
    public function setText(string $text): View
    {
        $this->text = $text;
        $this->textChecksum = null;
        $this->raw = null;
        $this->tree = null;
        return $this;
    }

    /**
     * validateName validates the name of the view.
     * This can be used to ensure the YAML files have proper/expected names
     * @return bool
     */
    public function validateName(): bool
    {
        if (empty($this->name)) {
            return false;
        }
        if (preg_match('/[!@#\$%^&*\/\\\()]/', $this->name)) {
            return false;
        }
        return true;
    }

    /**
     * getName returns the name of this View
     * @return ?string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * setName sets the name for this View
     * @param string $name
     * @return View
     */
    public function setName(string $name): View
    {
        $this->name = $name;
        return $this;
    }
}
