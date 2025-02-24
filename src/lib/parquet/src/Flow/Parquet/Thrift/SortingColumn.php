<?php

declare(strict_types=1);
namespace Flow\Parquet\Thrift;

/**
 * Autogenerated by Thrift Compiler (0.18.1).
 *
 * DO NOT EDIT UNLESS YOU ARE SURE THAT YOU KNOW WHAT YOU ARE DOING
 *
 *  @generated
 */
use Thrift\Type\{TType};

/**
 * Sort order within a RowGroup of a leaf column.
 */
class SortingColumn
{
    public static $_TSPEC = [
        1 => [
            'var' => 'column_idx',
            'isRequired' => true,
            'type' => TType::I32,
        ],
        2 => [
            'var' => 'descending',
            'isRequired' => true,
            'type' => TType::BOOL,
        ],
        3 => [
            'var' => 'nulls_first',
            'isRequired' => true,
            'type' => TType::BOOL,
        ],
    ];

    public static $isValidate = false;

    /**
     * The ordinal position of the column (in this row group) *.
     *
     * @var int
     */
    public $column_idx;

    /**
     * If true, indicates this column is sorted in descending order. *.
     *
     * @var bool
     */
    public $descending;

    /**
     * If true, nulls will come before non-null values, otherwise,
     * nulls go at the end.
     *
     * @var bool
     */
    public $nulls_first;

    public function __construct($vals = null)
    {
        if (is_array($vals)) {
            if (isset($vals['column_idx'])) {
                $this->column_idx = $vals['column_idx'];
            }

            if (isset($vals['descending'])) {
                $this->descending = $vals['descending'];
            }

            if (isset($vals['nulls_first'])) {
                $this->nulls_first = $vals['nulls_first'];
            }
        }
    }

    public function getName()
    {
        return 'SortingColumn';
    }

    public function read($input)
    {
        $xfer = 0;
        $fname = null;
        $ftype = 0;
        $fid = 0;
        $xfer += $input->readStructBegin($fname);

        while (true) {
            $xfer += $input->readFieldBegin($fname, $ftype, $fid);

            if ($ftype == TType::STOP) {
                break;
            }

            switch ($fid) {
                case 1:
                    if ($ftype == TType::I32) {
                        $xfer += $input->readI32($this->column_idx);
                    } else {
                        $xfer += $input->skip($ftype);
                    }

                    break;
                case 2:
                    if ($ftype == TType::BOOL) {
                        $xfer += $input->readBool($this->descending);
                    } else {
                        $xfer += $input->skip($ftype);
                    }

                    break;
                case 3:
                    if ($ftype == TType::BOOL) {
                        $xfer += $input->readBool($this->nulls_first);
                    } else {
                        $xfer += $input->skip($ftype);
                    }

                    break;

                default:
                    $xfer += $input->skip($ftype);

                    break;
            }
            $xfer += $input->readFieldEnd();
        }
        $xfer += $input->readStructEnd();

        return $xfer;
    }

    public function write($output)
    {
        $xfer = 0;
        $xfer += $output->writeStructBegin('SortingColumn');

        if ($this->column_idx !== null) {
            $xfer += $output->writeFieldBegin('column_idx', TType::I32, 1);
            $xfer += $output->writeI32($this->column_idx);
            $xfer += $output->writeFieldEnd();
        }

        if ($this->descending !== null) {
            $xfer += $output->writeFieldBegin('descending', TType::BOOL, 2);
            $xfer += $output->writeBool($this->descending);
            $xfer += $output->writeFieldEnd();
        }

        if ($this->nulls_first !== null) {
            $xfer += $output->writeFieldBegin('nulls_first', TType::BOOL, 3);
            $xfer += $output->writeBool($this->nulls_first);
            $xfer += $output->writeFieldEnd();
        }
        $xfer += $output->writeFieldStop();
        $xfer += $output->writeStructEnd();

        return $xfer;
    }
}
