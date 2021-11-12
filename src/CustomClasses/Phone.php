<?php

namespace App\CustomClasses;

use JsonSerializable;

class Phone implements JsonSerializable
{
    public $country_code = '';
    public $area_code = '';
    public $city_code = '';
    public $last_code = '';
    public $extension_number = '';
    public $with_extension = '';
    public $number = '';

    const FORMATS = [
        'AAA-YYY-XXXX',
        'AAA YYY XXXX',
        'AAAYYYXXXX',
        '(AAA)YYY-XXXX',
        '(AAA)YYY XXXX',
        '(AAA)YYYXXXX',
    ];

    /**
     * 
     * @param string|array|null $number 
     * @return void 
     */
    public function __construct($number = null)
    {
        $this->number = $number;
        $this->setExtensionNumber($number);
        $number = string($number)->before('[');
        $this->setNumber($number);
        $this->with_extension = $this->format() . $this->appendExtensionFormated();
    }

    public function setNumber(string $number)
    {
        $number = $this->cleanup($number);
        $parts = $this->getParts($number);

        if (count($parts) == 4) {
            $this->area_code = $parts[1];
            $this->city_code = $parts[2];
            $this->last_code = $parts[3];
        }
    }

    public function isValid()
    {
        if($this->hasInvalidChars()){
            return false;
        }

        if( !$this->area_code || !is_numeric($this->area_code)){
            return false;
        }

        if( !$this->city_code || !is_numeric($this->city_code)){
            return false;
        }

        if( !$this->last_code || !is_numeric($this->last_code)){
            return false;
        }

        if($this->extension_number && !is_numeric($this->extension_number)){
            return false;
        }
        
        return true;
    }

    public function hasInvalidChars()
    {
        return  preg_match('/[A-Za-z]+/', $this->number);
    }

    public function toRaw()
    {
        return $this->format(). $this->appendExtension();
    }

    public function appendExtension()
    {
        if($this->extension_number){
            return '['.$this->extension_number.']';
        }
        return null;
    }

    public function appendExtensionFormated()
    {
        if($this->extension_number){
            return string($this->extension_number)->prepend(' (内線 ')->append(')');
        }
        return null;
    }

    // se o number conter o "[", assume que se trata do ramal, então seta o ramal -> ($this->extension_number) 
    public function setExtensionNumber(string $number = null)
    {
        if(string($number)->contains('[')){
            $this->extension_number = (string) string($number)->after('[')->before(']');
        }
    }

    public function fromJson(string $number): ?object
    {
        $number = json_decode($number);

        return $number;
    }

    public function cleanup($value)
    {
        $value = preg_replace("/[^0-9]/", "", $value);
        return $value;
    }

    public function format(string $format=null)
    {
        $format = $format??self::FORMATS[0];
        if (in_array($format,self::FORMATS)) {
            $format = $format;
        } else {
            $format = self::FORMATS[0];
        }

        $search = ['AAA','YYY','XXXX'];
        $replace = [
            'AAA'=>$this->area_code,
            'YYY'=>$this->city_code,
            'XXXX'=>$this->last_code
        ];
        if($this->isValid()){
            $phone = str_replace($search,$replace,$format);
            return $phone;
        }
        return '';
    }

    public function getParts($value): array
    {
        $number = $this->cleanup($value);

        $matches = [];
        switch (strlen($number)) {
            case 10:
                $areacodes = implode('|',$this->areaCodes());
                $specialCodes10 = implode('|',$this->specialCodes10());
                $regex1 = "/^($areacodes)(\d{1,4})(\d{4})$/";
                $regex2 = "/^($specialCodes10)(\d{1,4})(\d{4})$/";
                if(preg_match($regex2,$number,$matches)) {
                    return $matches;
                } else if(preg_match($regex1,$number,$matches)) {
                    return $matches;
                }
                break;
            case 11:
                $cellphoneCodes = implode('|',$this->cellphoneCodes());
                $specialCodes11 = implode('|',$this->specialCodes11());
                $regex1 = "/^($cellphoneCodes)(\d{1,4})(\d{4})$/";
                $regex2 = "/^($specialCodes11)(\d{1,4})(\d{4})$/";
                if(preg_match($regex2,$number,$matches)) {
                    return $matches;
                } else if(preg_match($regex1,$number,$matches)) {
                    return $matches;
                }
                break;
        }

        return [];
    }

    public function __toString()
    {
        return $this->format();
    }

    public function jsonSerialize()
    {
        return $this->number;
    }

    public function specialCodes10()
    {
        return ['0990', '0570', '0180', '0170', '0120'];
    }

    public function specialCodes11()
    {
        return ['0800'];
    }

    public function cellphoneCodes()
    {
        return ['090', '080', '070', '060', '050', '020'];
    }

    public function areaCodes()
    {
        return [
            '0997',
            '09969',
            '0996',
            '0995',
            '0994',
            '0993',
            '09913',
            '09912',
            '099',
            '0987',
            '0986',
            '0985',
            '0984',
            '0983',
            '0982',
            '09802',
            '0980',
            '098',
            '0979',
            '0978',
            '0977',
            '0974',
            '0973',
            '0972',
            '097',
            '0969',
            '0968',
            '0967',
            '0966',
            '0965',
            '0964',
            '096',
            '0959',
            '0957',
            '0956',
            '0955',
            '0954',
            '0952',
            '0950',
            '095',
            '09496',
            '0949',
            '0948',
            '0947',
            '0946',
            '0944',
            '0943',
            '0942',
            '0940',
            '0930',
            '093',
            '0920',
            '092',
            '0898',
            '0897',
            '0896',
            '0895',
            '0894',
            '0893',
            '0892',
            '089',
            '0889',
            '0887',
            '0885',
            '0884',
            '0883',
            '0880',
            '088',
            '0879',
            '0877',
            '0875',
            '087',
            '0869',
            '0868',
            '0867',
            '0866',
            '0865',
            '0863',
            '086',
            '0859',
            '0858',
            '0857',
            '0856',
            '0855',
            '0854',
            '0853',
            '0852',
            '08514',
            '08512',
            '0848',
            '08477',
            '0847',
            '0846',
            '0845',
            '084',
            '08396',
            '08388',
            '08387',
            '0838',
            '0837',
            '0836',
            '0835',
            '0834',
            '0833',
            '083',
            '0829',
            '0827',
            '0826',
            '0824',
            '0823',
            '0820',
            '082',
            '0799',
            '0798',
            '0797',
            '0796',
            '0795',
            '0794',
            '0791',
            '0790',
            '079',
            '078',
            '0779',
            '0778',
            '0776',
            '0774',
            '0773',
            '0772',
            '0771',
            '0770',
            '077',
            '0768',
            '0767',
            '0766',
            '0765',
            '0763',
            '0761',
            '076',
            '075',
            '0749',
            '0748',
            '0747',
            '07468',
            '0746',
            '0745',
            '0744',
            '0743',
            '0742',
            '0740',
            '0739',
            '0738',
            '0737',
            '0736',
            '0735',
            '073',
            '0725',
            '0721',
            '072',
            '06',
            '0599',
            '0598',
            '05979',
            '0597',
            '0596',
            '0595',
            '0594',
            '059',
            '0587',
            '0586',
            '0585',
            '0584',
            '0581',
            '058',
            '0578',
            '0577',
            '05769',
            '0576',
            '0575',
            '0574',
            '0573',
            '0572',
            '0569',
            '0568',
            '0567',
            '0566',
            '0565',
            '0564',
            '0563',
            '0562',
            '0561',
            '0558',
            '0557',
            '0556',
            '0555',
            '0554',
            '0553',
            '0551',
            '0550',
            '055',
            '0548',
            '0547',
            '0545',
            '0544',
            '054',
            '0539',
            '0538',
            '0537',
            '0536',
            '0533',
            '0532',
            '0531',
            '053',
            '052',
            '04998',
            '04996',
            '04994',
            '04992',
            '0495',
            '0494',
            '0493',
            '049',
            '0480',
            '048',
            '0479',
            '0478',
            '0476',
            '0475',
            '0470',
            '047',
            '0467',
            '0466',
            '0465',
            '0463',
            '0460',
            '046',
            '045',
            '044',
            '0439',
            '0438',
            '0436',
            '043',
            '0428',
            '0422',
            '042',
            '04',
            '03',
            '0299',
            '0297',
            '0296',
            '0295',
            '0294',
            '0293',
            '0291',
            '029',
            '0289',
            '0288',
            '0287',
            '0285',
            '0284',
            '0283',
            '0282',
            '0280',
            '028',
            '0279',
            '0278',
            '0277',
            '0276',
            '0274',
            '0270',
            '027',
            '0269',
            '0268',
            '0267',
            '0266',
            '0265',
            '0264',
            '0263',
            '0261',
            '0260',
            '026',
            '0259',
            '0258',
            '0257',
            '0256',
            '0255',
            '0254',
            '0250',
            '025',
            '0248',
            '0247',
            '0246',
            '0244',
            '0243',
            '0242',
            '0241',
            '0240',
            '024',
            '0238',
            '0237',
            '0235',
            '0234',
            '0233',
            '023',
            '0229',
            '0228',
            '0226',
            '0225',
            '0224',
            '0223',
            '0220',
            '022',
            '0198',
            '0197',
            '0195',
            '0194',
            '0193',
            '0192',
            '0191',
            '019',
            '0187',
            '0186',
            '0185',
            '0184',
            '0183',
            '0182',
            '018',
            '0179',
            '0178',
            '0176',
            '0175',
            '0174',
            '0173',
            '0172',
            '017',
            '0167',
            '0166',
            '01658',
            '01656',
            '01655',
            '01654',
            '0165',
            '01648',
            '0164',
            '01635',
            '01634',
            '01632',
            '0163',
            '0162',
            '01587',
            '01586',
            '0158',
            '0157',
            '01564',
            '0156',
            '01558',
            '0155',
            '01547',
            '0154',
            '0153',
            '0152',
            '015',
            '01466',
            '0146',
            '01457',
            '01456',
            '0145',
            '0144',
            '0143',
            '0142',
            '01398',
            '01397',
            '01392',
            '0139',
            '0138',
            '01377',
            '01374',
            '01372',
            '0137',
            '0136',
            '0135',
            '0134',
            '0133',
            '01267',
            '0126',
            '0125',
            '0124',
            '0123',
            '011',
        ];
    }
}