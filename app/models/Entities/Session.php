<?php

namespace Models\Entities;

use \Models\Entities\Generated;
use \Doctrine\ORM\Mapping\Entity;
use \Doctrine\ORM\Mapping\Table;
/**
 * @Entity(repositoryClass="\Models\Repositories\Session")
 * @Table(name="tbl_session")
 */
class Session extends Generated\Session
{
    const AREA_CUSTOMER = 'CUSTOMER';
    const AREA_MANAGER = 'MANAGER';
    const LIMIT_LOAD_MORE = 5;
    const ARR_DEVICE_TYPE_ICON = [
        'fa-solid fa-desktop',
        'fa-solid fa-mobile',
        'fa-solid fa-tablet',
        'fa-solid fa-mobile-retro',
        'fa-solid fa-game-console-handheld',
        'fa-solid fa-tv',
        'fa-solid fa-car',
        'fa-solid fa-display',
        'fa-solid fa-camera',
        'fa-solid fa-mp3-player',
        'fa-light fa-tablet-button',
        'fa-solid fa-speaker',
        'fa-solid fa-watch-smart',
        'fa-solid fa-print',
        'fa-solid fa-square-question',
    ];
    const ARR_DEVICE_TYPE_NAME = [
        'Desktop',
        'Smartphone',
        'Tablet',
        'Feature phone',
        'Console',
        'TV',
        'Car browser',
        'Smart display',
        'Camera',
        'Portable media player',
        'Phablet',
        'Smart speaker',
        'Wearable',
        'Peripheral',
        'Unknown',
    ];
    /**
     * Set screen information
     *
     * @param array $data
     * @return self
     */
    public function setSs_sr_info(array $data = []): self
    {
        $json = empty($data) 
            ? '{}'
            : @json_encode($data);
        $this->ss_sr_info = $json;

        return $this;
    }

    /**
     * Return screen information
     *
     * @return array
     */
    public function getSs_sr_info(): array
    {
        if ($this->ss_sr_info) {
            return @json_decode($this->ss_sr_info, true);
        }
        return [];
    }

    /**
     * Return sesison id
     *
     * @return string
     */
    public function getSsId(): string
    {
        return $this->ss_id;
    }

    /**
     * Set session id
     *
     * @param string $ss_id
     * @return void
     */
    public function setSsId(string $ss_id): void
    {
        $this->ss_id = $ss_id;
    }
}

?>