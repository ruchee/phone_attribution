<?php

namespace Ruchee\PhoneAttribution;

/*
 * 解析手机号码归属地
 * @author Ruchee
 * @date 2018-03-22
 */
class Parse
{
    // 运营商列表
    private $sp_list = [
        1 => '中国移动',
        2 => '中国联通',
        3 => '中国电信',
        4 => '电信虚拟运营商',
        5 => '联通虚拟运营商',
        6 => '移动虚拟运营商',
    ];

    // 返回数据
    private $return_data = [
        'status' => 1,
        'info'   => '解析成功',
        'data'   => [],
    ];

    private $data_file   = __DIR__.'/../data/phone.dat';
    private $file_handle = null;
    private $file_size   = 0;

    public function __construct(string $custom_data_file = null)
    {
        if (! empty($custom_data_file)) {
            $this->data_file = $custom_data_file;
        }
        if (! file_exists($this->data_file)) {
            $this->return_data['status'] = 0;
            $this->return_data['info']   = '数据文件不存在';
            return ;
        }

        $this->file_handle = fopen($this->data_file, 'r');
        $this->file_size   = filesize($this->data_file);
    }

    public function __destruct()
    {
        if (! empty($this->file_handle)) {
            fclose($this->file_handle);
        }
    }

    /*
     * 解析单个号码
     */
    public function parseOne(string $phone) : array
    {
        if ($this->return_data['status'] != 1) {
            return $this->return_data;
        }
        if (empty($phone)) {
            $this->return_data['status'] = 0;
            $this->return_data['info']   = '手机号码不能为空';
            return $this->return_data;
        }

        $this->return_data['data'] = $this->parse(trim($phone));

        return $this->return_data;
    }

    /*
     * 解析多个号码
     */
    public function parseList(array $phone_list) : array
    {
        if ($this->return_data['status'] != 1) {
            return $this->return_data;
        }
        if (empty($phone_list)) {
            $this->return_data['status'] = 0;
            $this->return_data['info']   = '手机号码列表不能为空';
            return $this->return_data;
        }

        $this->return_data['data'] = [];
        foreach ($phone_list as $phone) {
            $this->return_data['data'][trim($phone)] = $this->parse(trim($phone));
        }

        return $this->return_data;
    }

    /*
     * 具体的解析算法
     */
    private function parse(string $phone) : array
    {
        if (strlen($phone) != 11) {
            return [];
        }

        $data   = [];
        $search = substr($phone, 0, 7);

        fseek($this->file_handle, 4);

        $offset      = fread($this->file_handle, 4);
        $index_start = implode('', unpack('L', $offset));
        $total       = ($this->file_size - $index_start) / 9;

        [$low, $mid, $hig] = [0, 0, $total - 1];

        // 二分查找
        while ($low <= $hig) {
            $mid = $low + floor(($hig - $low) / 2);  // 当前猜测位

            fseek($this->file_handle, ($mid * 9) + $index_start);
            $gus = implode('', unpack('L', fread($this->file_handle, 4)));  // 当前猜测值

            if ($gus == $search) {
                fseek($this->file_handle, ($mid * 9 + 4) + $index_start);

                $info    = unpack('Lgus_pos/ctype', fread($this->file_handle, 5));
                $pos     = $info['gus_pos'];
                $type    = $info['type'];
                $spname  = $this->sp_list[$type];

                fseek($this->file_handle, $pos);

                $info = '';
                while (($tmp = fread($this->file_handle, 1)) != chr(0)) {
                    $info .= $tmp;
                }

                $info     = explode('|', $info);
                $data     = [
                    'province' => $info[0], // 省份
                    'city'     => $info[1], // 城市
                    'postcode' => $info[2], // 邮编
                    'prefix'   => $info[3], // 区号
                    'spname'   => $spname,  // 运营商
                ];

                break;
            }

            $gus < $search ? $low = $mid + 1 : $hig = $mid - 1;
        }

        return $data;
    }
}
