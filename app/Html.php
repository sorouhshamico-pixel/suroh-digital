<?php
namespace App;
/** Allowlisted blog formatting; never retain scriptable elements or attributes. */
final class Html {
 public static function sanitize(string $html, bool $forDisplay=false):string{
  $dom=new \DOMDocument();$previous=libxml_use_internal_errors(true);
  $dom->loadHTML('<?xml encoding="UTF-8"><div id="sd-root">'.$html.'</div>',LIBXML_HTML_NOIMPLIED|LIBXML_HTML_NODEFDTD|LIBXML_NONET);
  libxml_clear_errors();libxml_use_internal_errors($previous);
  $root=$dom->getElementById('sd-root');if(!$root)return '';
  self::clean($root, $forDisplay);$result='';foreach($root->childNodes as $child)$result.=$dom->saveHTML($child);return $result;
 }
 private static function clean(\DOMNode $node, bool $forDisplay):void{
  $allowed=['p','br','h2','h3','h4','strong','b','em','i','ul','ol','li','blockquote','a','img','figure','figcaption','table','thead','tbody','tr','th','td','hr','pre','code','div','span'];
  foreach(iterator_to_array($node->childNodes) as $child){
   if($child instanceof \DOMElement){
    $tag=strtolower($child->tagName);
    if(!in_array($tag,$allowed,true)){$node->removeChild($child);continue;}
    foreach(iterator_to_array($child->attributes) as $attr){
     $name=strtolower($attr->name);$value=trim($attr->value);
     $keep=($tag==='a'&&$name==='href'&&self::safeUrl($value))||($tag==='img'&&$name==='src'&&self::safeUrl($value))||($tag==='img'&&$name==='alt')||($name==='title')||($name==='dir'&&in_array($value,['rtl','ltr'],true));
     if(!$keep)$child->removeAttribute($attr->name);
    }
    if($forDisplay && in_array($tag,['img','a'],true)){
     $attribute=$tag==='img'?'src':'href';
     if($child->hasAttribute($attribute))$child->setAttribute($attribute,media_url($child->getAttribute($attribute)));
    }
    if($tag==='img'){$child->setAttribute('loading','lazy');$child->setAttribute('decoding','async');}
    if($tag==='a')$child->setAttribute('rel','noopener noreferrer');
    self::clean($child, $forDisplay);
   }elseif(!($child instanceof \DOMText))$node->removeChild($child);
  }
 }
 public static function safeUrl(string $url):bool {
  if(preg_match('/[\x00-\x20\\\\]/',$url))return false;
  return (str_starts_with($url,'/')&&!str_starts_with($url,'//')) || (filter_var($url,FILTER_VALIDATE_URL)&&in_array(strtolower(parse_url($url,PHP_URL_SCHEME)??''),['http','https'],true));
 }
}
