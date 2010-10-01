<?php
/**
 * Du
 *
 * LICENSE
 *
 * This source file is subject to the BSD license that is available
 * through the world-wide-web at this URL:
 * http://opensource.org/licenses/bsd-license.php
 *
 * @category   Du
 * @package    Du_BundleFu
 * @copyright  Copyright (C) 2010 - Present, Jan Sorgalla
 * @license    BSD License {@link http://www.opensource.org/licenses/bsd-license.php}
 */

namespace Du\BundleFu;

/**
 * Du\BundleFu\CssUrlRewriter
 *
 * @category   Du
 * @package    Du_BundleFu
 * @author     Jan Sorgalla
 * @copyright  Copyright (C) 2010 - Present, Jan Sorgalla
 * @license    BSD License {@link http://www.opensource.org/licenses/bsd-license.php}
 * @version    Release: @package_version@
 */
class CssUrlRewriter
{
    /**
     * Rewrites relative urls in css files.
     *
     * @param string $filename
     * @param string $content
     * @return string
     */
    public function rewriteUrls($filename, $content)
    {
        $self = $this;
        return preg_replace_callback('/url *\(([^\)]+)\)/', function($matches) use ($self, $filename) {
            $relativeUrl = trim($matches[1], ' "\'');
            return 'url(' . $self->rewriteRelativePath($filename, $relativeUrl) . ')';
        }, $content);
    }

    /**
     * Rewrites relative urls depending on a base url.
     *
     * @param string $baseUrl
     * @param string $relativeUrl
     * @return string
     */
    public function rewriteRelativePath($baseUrl, $relativeUrl)
    {
        if ($relativeUrl[0] == '/' || strpos($relativeUrl, '://') !== false) {
            $absoluteUrl = $relativeUrl;
        } else {
            if (preg_match('/\.[a-z0-9]+$/i', $baseUrl)) {
                $baseUrl = dirname($baseUrl);
            }

            $path = trim($baseUrl, '/') . '/' . $relativeUrl;

            $parts     = array_filter(explode('/', $path));
            $absolutes = array();

            foreach ($parts as $part) {
                if ('.' == $part) {
                    continue;
                }

                if ('..' == $part) {
                    array_pop($absolutes);
                } else {
                    $absolutes[] = $part;
                }
            }

            $absoluteUrl = '/' . implode('/', $absolutes);
        }

        return $absoluteUrl;
    }
}
