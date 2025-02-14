<?php

/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2022 (original work) Open Assessment Technologies SA;
 *
 * @author Gabriel Felipe Soares <gabriel.felipe.soares@taotesting.com>
 */

declare(strict_types=1);

namespace oat\tao\test\unit\model\Language;

use oat\tao\model\Language\Language;
use PHPUnit\Framework\TestCase;
use tao_models_classes_LanguageService;

class LanguageTest extends TestCase
{
    public function testGetters(): void
    {
        $language = new Language(
            'uri',
            'pt-BR',
            'Portuguese',
            tao_models_classes_LanguageService::INSTANCE_ORIENTATION_LTR,
            null
        );
        $this->assertSame('uri', $language->getUri());
        $this->assertSame('pt-BR', $language->getCode());
        $this->assertSame('Portuguese', $language->getLabel());
        $this->assertSame(tao_models_classes_LanguageService::INSTANCE_ORIENTATION_LTR, $language->getOrientation());
        $this->assertSame(null, $language->getVerticalWritingMode());

        $language = new Language(
            'uri',
            'ja-JP',
            'Japanese',
            'ltr',
            'vertical-rl'
        );
        $this->assertSame('vertical-rl', $language->getVerticalWritingMode());
    }

    public function testJsonSerialize(): void
    {
        $language = new Language(
            'uri',
            'pt-BR',
            'Portuguese',
            tao_models_classes_LanguageService::INSTANCE_ORIENTATION_LTR,
            null
        );
        $this->assertSame(
            [
                'uri' => $language->getUri(),
                'code' => $language->getCode(),
                'label' => $language->getLabel(),
                'orientation' => $language->getOrientation(),
            ],
            $language->jsonSerialize()
        );

        $language = new Language(
            'uri',
            'ja-JP',
            'Japanese',
            'ltr',
            'vertical-rl'
        );
        $this->assertSame(
            [
                'uri' => $language->getUri(),
                'code' => $language->getCode(),
                'label' => $language->getLabel(),
                'orientation' => $language->getOrientation(),
                'verticalWritingMode' => $language->getVerticalWritingMode()
            ],
            $language->jsonSerialize()
        );
    }
}
