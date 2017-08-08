<?php
/**
 * This file is part of PHPWord - A pure PHP library for reading and writing
 * word processing documents.
 *
 * PHPWord is free software distributed under the terms of the GNU Lesser
 * General Public License version 3 as published by the Free Software Foundation.
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code. For the full list of
 * contributors, visit https://github.com/PHPOffice/PHPWord/contributors.
 *
 * @link        https://github.com/PHPOffice/PHPWord
 * @copyright   2010-2014 PHPWord contributors
 * @license     http://www.gnu.org/licenses/lgpl.txt LGPL version 3
 */

namespace PhpOffice\PhpWord;

/**
 * @deprecated 0.12.0 Use \PhpOffice\PhpWord\TemplateProcessor instead.
 */
class Template extends TemplateProcessor
{
	
	//замена ссылок на картинки
	// идея взята отсюда:
	//http://phpword.codeplex.com/discussions/262976
	public function save_images($mas_img) { 
	
    	if(count($mas_img)>0) //если еще не извлекали эту картинку
    	{
			foreach ($mas_img as $img_patch){//обходим массив картинок которые вытащены из doc файла
				
				$path_parts = pathinfo($img_patch);
				$id_img='word/media/'.$path_parts['filename'].".".$path_parts['extension']; //имя картинки внутри doc
				
				$this->setValue($id_img, $img_patch); //$this->setValue($id_img.'::width', "300px");  //- задать размеры можно так			
				
				//var_dump($this);
			}
    	} 
    }
	
	
}
