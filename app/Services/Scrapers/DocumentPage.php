<?php

namespace App\Services\Scrapers;

use App\Services\HttpClient;
use Symfony\Component\DomCrawler\Crawler;

class DocumentPage
{
    private $client;

    public function __construct(HttpClient $client)
    {
        $this->client = $client;
    }

    public function clearHtml($html)
    {
        $decoded = '';

        for ($i = 0; $i < strlen($html); $i++) {
            $char = substr($html, $i, 1);

            if (ord($char) >= 32) {
                $decoded .= $char;
            }
        }

        $decoded = trim($decoded);

        //        $decoded = $this->removeEmptyTable($decoded);

        return $decoded;
    }

    private function removeEmptyTable($decoded)
    {
        $string =
            '<table width="100%" border="1"><tr valign="top"><td width="100%"><img width="1" height="1" src="/icons/ecblank.gif" border="0" alt=""></td></tr></table>';

        return substr($decoded, 0, strpos($decoded, $string)) .
            substr($decoded, strpos($decoded, $string) + strlen($string));
    }

    public function scrape($base_url, $item)
    {
        $url = "$base_url/$item?OpenDocument";

        $page = $this->client->getRaw($url);

        $crawler = new Crawler($page);

        $crawler = $crawler->filter('body');

        return $this->clearHtml($crawler->html());
    }
}

//
////This code will be placed in the JSHeader of the page var columns; var entries; var nIndex; var nRows; var valuList; var arrID = new Array();
//
//var xmlDoc=new ActiveXObject("Microsoft.XMLDOM");
//function loadXML(xmlFile)
//{
//    xmlDoc.async="false";
//    xmlDoc.validateOnParse = false;
//    xmlDoc.load(xmlFile);
//}
//
//function traverse(tree)
//{
//    entries = tree.getElementsByTagName("viewentry");
//
//    document.write("&lt;html&gt; &lt;body&gt;");
//    document.write("&lt;font size='5' color='rgb(173,15,31)'&gt; &lt;i&gt;  View in Table &lt;/i&gt; &lt;/font&gt; &lt;br&gt;");
//    document.write("&lt;hr size='2' width='170' align='left'&gt; &lt;br&gt;");
//
//    KeyNode=tree.selectNodes("/viewentries/viewentry/entrydata&#91;@columnnumber='0'&#93;");
//    columnNode=tree.selectNodes("/viewentries/viewentry/entrydata&#91;@columnnumber='1'&#93;");
//
//    document.write("&lt;table border='1'&gt;");
//    document.write("&lt;tr&gt;&lt;th&gt;S.No&lt;/th&gt; &lt;th&gt; Keyword &lt;/th&gt; &lt;th&gt; Values &lt;/th&gt; &lt;/tr&gt;");
//    for(nRows=0;nRows&lt;KeyNode.length;nRows++)
//	{
//// This is to get the unique id of the notes document to provide a link to it
//        arrID&#91;nRows&#93; = entries&#91;nRows&#93;.getAttribute('unid');
//        document.write("&lt;tr&gt; &lt;th&gt; &lt;a href=Keywords/"+arrID&#91;nRows&#93;+"?OpenDocument&gt;"+parseInt(nRows+1) + "&lt;/a&gt; &lt;/th&gt;");
//            document.write(" &lt;th&gt;" + KeyNode&#91;nRows&#93;.text + "&lt;/th&gt; &lt;td&gt;");
//            columns = columnNode&#91;nRows&#93;.getElementsByTagName("text");
//            // Add a comma separator if multiple data available. ie, a text list of entries exist
//            valuList = "";
//        for (nIndex=0;nIndex&lt;columns.length;nIndex++)
//		{
//            if (columns.length&gt;1)
//			{
//                valuList = valuList + columns&#91;nIndex&#93;.text;
//				if (nIndex!=(columns.length-1))
//                {
//                    valuList = valuList + ", ";
//                }
//			}
//			else
//			{
//                valuList = columns&#91;nIndex&#93;.text;
//			}
//		}
//	document.write(valuList + "&lt;/td&gt; &lt;/tr&gt;");
//	}
//	document.write("&lt;/table&gt; &lt;/body&gt; &lt;/html&gt;");
//}
//
//function initTraverse()
//{
//    loadXML("http://hostname/Path/DominoDevSample.nsf/Keywords?ReadViewEntries");
//    var root=xmlDoc.documentElement;
//    traverse(root);
//}
//
////Call the function from the onLoad event of the page initTraverse();
//
