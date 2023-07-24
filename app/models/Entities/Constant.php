<?php

namespace Models\Entities;

use \Models\Entities\Generated;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Table;

/**
 * @Entity(repositoryClass="\Models\Repositories\Constant")
 * @Table(name="tbl_constant")
 */
class Constant extends Generated\Constant
{
    /**
     * Mode on
     * @var string
     */
    const STATE_ON = 'ON';
    /**
     * Mode off
     * @var string
     */
    const STATE_OFF = 'OFF';
    /**
     * Constant html: use text editor
     * @var string
     */
    const MODE_HTML = 'HTML';
    /**
     * Constant text: use text input
     * @var string
     */
    const MODE_TEXT = 'TEXT';
    const TEMPLATE_KEYS = [
        '{{fullname}}', '{{time}}', '{{money}}',
        '{{title}}', '{{lss_title}}', '{{teacher_name}}',
        '{{cost}}', '{{chat_unit}}', '{{chat_test}}',
        '{{chat_room}}', '{{link_to_payment}}',
        '{{learning_time}}', '{{price}}', '{{lesson_img}}', '{{lss_title}}',
        '{{link_to_lesson}}', '{{owner_fullname}}', '{{link_authentication}}',
        '{{__link_active__}}', '{{teacher_name}}', '{{student_name}}'
    ];
}

?>