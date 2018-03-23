<?php

use PHPUnit\Framework\TestCase;
use Ruchee\PhoneAttribution\Parse;

class ParseTest extends TestCase
{
    public function setUp()
    {
        $this->parse    = new Parse();  // 默认数据源
        $this->parse_ok = new Parse(__DIR__.'/../data/phone.dat');  // 自定义正确的数据源
        $this->parse_no = new Parse(__DIR__.'/../data/error.dat');  // 自定义错误的数据源

        $this->phone            = '13713462969';                  // 合法的手机号码
        $this->phone_no         = '137134629691';                 // 非法的手机号码
        $this->phone_blank      = '';                             // 空白的手机号码
        $this->phone_has_blank  = ' 13713462969 ';                // 带空格的手机号码
        $this->phone_list       = ['18520796150', '18745222111']; // 合法的手机号码列表
        $this->phone_list_blank = [];                             // 空白的手机号码列表

        // 单个号码的正确返回值
        $this->ret_one = [
            'status' => 1,
            'info'   => '解析成功',
            'data'   => [
                'province' => '广东',
                'city'     => '东莞',
                'postcode' => '523000',
                'prefix'   => '0769',
                'spname'   => '中国移动',
            ],
        ];

        // 多个号码的正确返回值
        $this->ret_list = [
            'status' => 1,
            'info'   => '解析成功',
            'data'   => [
                '18520796150' => [
                    'province' => '广东',
                    'city'     => '广州',
                    'postcode' => '510000',
                    'prefix'   => '020',
                    'spname'   => '中国联通',
                ],
                '18745222111' => [
                    'province' => '黑龙江',
                    'city'     => '齐齐哈尔',
                    'postcode' => '161000',
                    'prefix'   => '0452',
                    'spname'   => '中国移动',
                ],
            ],
        ];
    }

    /*
     * 解析单个号码
     */
    public function testParseOne()
    {
        $ret = $this->parse->parseOne($this->phone);
        $wat = $this->ret_one;

        $this->assertSame($ret, $wat);
    }

    /*
     * 解析多个号码
     */
    public function testParseList()
    {
        $ret = $this->parse->parseList($this->phone_list);
        $wat = $this->ret_list;

        $this->assertSame($ret, $wat);
    }

    /*
     * 解析非法的号码
     */
    public function testParseOneError()
    {
        $ret = $this->parse->parseOne($this->phone_no);

        $this->assertSame(0, $ret['status']);
        $this->assertSame('解析失败', $ret['info']);
    }

    /*
     * 解析空白的号码
     */
    public function testParseOneBlank()
    {
        $ret = $this->parse->parseOne($this->phone_blank);

        $this->assertSame(0, $ret['status']);
        $this->assertSame('手机号码不能为空', $ret['info']);
    }

    /*
     * 解析带空格的号码
     */
    public function testParseOneHasBlank()
    {
        $ret = $this->parse->parseOne($this->phone_has_blank);
        $wat = $this->ret_one;

        $this->assertSame($ret, $wat);
    }

    /*
     * 解析空白的号码列表
     */
    public function testParseListBlank()
    {
        $ret = $this->parse->parseList($this->phone_list_blank);

        $this->assertSame(0, $ret['status']);
        $this->assertSame('手机号码列表不能为空', $ret['info']);
    }

    /*
     * 自定义正确的数据源
     */
    public function testCustomDataSource()
    {
        $ret = $this->parse_ok->parseOne($this->phone);
        $wat = $this->ret_one;

        $this->assertSame($ret, $wat);
    }

    /*
     * 自定义错误的数据源
     */
    public function testCustomErrorDataSource()
    {
        $ret = $this->parse_no->parseOne($this->phone);

        $this->assertSame(0, $ret['status']);
        $this->assertSame('数据文件不存在', $ret['info']);
    }
}
