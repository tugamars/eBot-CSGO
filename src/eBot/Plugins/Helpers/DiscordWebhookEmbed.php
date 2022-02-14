<?php


namespace eBot\Plugins\Helpers;


class DiscordWebhookEmbed
{
    protected $json=array();
    /**
     * DiscordWebhook constructor.
     */
    public function __construct()
    {
        $this->json = array();
    }

    /**
     * @return array
     */
    public function getJson()
    {
        return $this->json;
    }

    public function addEmbed($title='',$description='‌', $url='', $author_name='', $color=23807, $thumbnailUrl=''){

        $arr=array(
            'description'=>substr($description,0,2047),
            'color' => $color,
            'thumbnail'=> array(
                'url'=>$thumbnailUrl
            )
        );

        if($title!=='') $arr["title"]=substr($title,0,256);
        if($url!=='') $arr["url"]=$url;
        if($author_name!=='') $arr["author"]=array('name'=>$author_name);

        $this->json[]=$arr;

        return sizeof($this->json)-1;
    }

    public function addField($title='‌', $value='‌',$inLine = false,$embed=0)
    {

        $this->json[$embed]['fields'][] = array(
            'name'   => substr($title,0,256),
            'value'  => substr($value,0,1024),
            'inline' => $inLine,
        );

        return $this;
    }

}