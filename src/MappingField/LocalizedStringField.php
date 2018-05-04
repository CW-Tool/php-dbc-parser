<?php

declare(strict_types=1);

namespace Wowstack\Dbc\MappingField;

class LocalizedStringField extends AbstractField implements MappingFieldInterface
{
    /**
     * Supported locales and their offset in the field.
     */
    const LOCALE_INDEX = [
        'enUS' => 0,
        'enGB' => 0,
        'koKR' => 1,
        'frFR' => 2,
        'deDE' => 3,
        'zhCN' => 4,
        'zhTW' => 5,
        'esES' => 6,
        'esMX' => 7,
    ];

    /**
     * Amount of bytes used by localization checksum.
     */
    const CHECKSUM_SIZE = 4;

    /**
     * Name of type.
     */
    const TYPE = 'localized_string';

    /**
     * {@inheritdoc}
     */
    protected $size = 4;

    /**
     * Amount of fields to follow.
     *
     * @var int
     */
    protected $count = 0;

    /**
     * Default locale to use.
     */
    protected $locale = 'enUS';

    /**
     * Default amount of locale fields to read.
     */
    protected $locale_count = 8;

    /**
     * Defines required parameters.
     */
    const PARAMETERS = ['count'];

    /**
     * Defines optional parameters and their defaults.
     */
    const OPTIONAL_PARAMETERS = ['locale' => 'enUS', 'locale_count' => 8];

    /**
     * Format used to pack/unpack this field type.
     */
    const PACK_FORMAT = 'V';

    /**
     * Constructs the field.
     *
     * @param string $name
     * @param array  $parameters
     */
    public function __construct(string $name, array $parameters = [])
    {
        $this->name = $name;
        $this->setParameters($parameters);
        $this->setOptionalParameters($parameters);
    }

    /**
     * {@inheritdoc}
     */
    public function getOffset(): int
    {
        return self::LOCALE_INDEX[$this->locale];
    }

    /**
     * {@inheritdoc}
     */
    public function getTotalCount(): int
    {
        return $this->count * ($this->locale_count + 1);
    }

    /**
     * {@inheritdoc}
     */
    public function getSize(): int
    {
        return ($this->size * $this->locale_count) + self::CHECKSUM_SIZE;
    }

    /**
     * {@inheritdoc}
     */
    public function getTotalSize(): int
    {
        return $this->getSize() * $this->count;
    }

    /**
     * {@inheritdoc}
     */
    public function getParsedFields(): array
    {
        $count = 1;
        $locale_count = $this->getLocaleCount();
        $string_offset = 0;
        $parsed_fields = [];

        while ($count <= $this->getCount()) {
            $field_name = ($this->getCount() > 1 ? $this->getName().$count : $this->getName());
            $pack_fields = [];
            while ($string_offset < $locale_count) {
                if ($string_offset === $this->getOffset()) {
                    $pack_fields[] = $this::PACK_FORMAT.'1'.$field_name;
                } else {
                    $pack_fields[] = $this::PACK_FORMAT.'1'.$field_name.'_unused'.$string_offset;
                }
                ++$string_offset;
            }
            $string_offset = 0;

            $pack_fields[] = $this::PACK_FORMAT.'1'.$field_name.'_checksum';

            $parsed_field = [
                'type' => $this->getType(),
                'size' => $this->getSize(),
                'format' => implode('/', $pack_fields),
                'offset' => $this->getOffset(),
            ];

            $parsed_fields[$field_name] = $parsed_field;
            ++$count;
        }

        return $parsed_fields;
    }

    /**
     * Returns the selected locale.
     *
     * @return string
     */
    public function getLocale(): string
    {
        return $this->locale;
    }

    /**
     * Returns the selected locale count.
     *
     * @return int
     */
    public function getLocaleCount(): int
    {
        return $this->locale_count;
    }
}
