<?php

declare(strict_types=1);

namespace Xml\Tests;

use Xml\Element;

class ElementTest extends TestCase
{
    const RSA_SHA1 = 'http://www.w3.org/2000/09/xmldsig#rsa-sha1';

    const SHA1 = 'http://www.w3.org/2000/09/xmldsig#sha1';

    const ENVELOPED_SIGNATURE = 'http://www.w3.org/2000/09/xmldsig#enveloped-signature';

    const XML_DSIG = 'http://www.w3.org/2000/09/xmldsig#';

    const XML_C14N = 'http://www.w3.org/TR/2001/REC-xml-c14n-20010315';

    public function testShouldCreateColombiaSign()
    {
        $signature = Element::create('ds:Signature', [
            'Id' => 'xmldsig-88fbfc45-3be2-4c4a-83ac-0796e1bad4c5',
            'xmlns:ds' => static::XML_DSIG
        ]);

        $signature->SignedInfo(function ($signedInfo) {
            $signedInfo->CanonicalizationMethod([
                'Algorithm' => static::XML_C14N
            ]);
            $signedInfo->SignatureMethod([
                'Algorithm' => static::RSA_SHA1
            ]);
            $signedInfo->Reference(
                [
                    'Id' => 'xmldsig-88fbfc45-3be2-4c4a-83ac-0796e1bad4c5-ref0',
                    'URI' => ''
                ],
                function ($reference) {
                    $reference->Transforms(function ($transforms) {
                        $transforms->Transform([
                            'Algorithm' => static::ENVELOPED_SIGNATURE
                        ]);
                    });
                    $reference->DigestMethod([
                        'Algorithm' => static::SHA1
                    ]);
                    $reference->DigestValue('6F5KPfMMBWPbl8ImvaG9z9NFSLE=');
                }
            );

            $signedInfo->Reference(function ($reference) {
                $reference['URI'] = '#xmldsig-87d128b5-aa31-4f0b-8e45-3d9cfa0eec26-keyinfo';
                $reference->DigestMethod([
                    'Algorithm' => static::SHA1
                ]);
                $reference->DigestValue('0iE/FGZgLfbnV9DhUaDBBVPjn44=');
            });

            $signedInfo->Reference(function ($reference) {
                $reference['Type'] = 'http://uri.etsi.org/01903#SignedProperties';
                $reference['URI'] = '#xmldsig-88fbfc45-3be2-4c4a-83ac-0796e1bad4c5-signedprops';
                $reference->DigestMethod([
                    'Algorithm' => static::SHA1
                ]);
                $reference->DigestValue('mnp1FDOGYZ97yw3pTeldFRVg+64=');
            });
        });

        $signature->SignatureValue('KhSG6Gats5f8HwyjC/3dG+GmkhwIVjIygwcA9SeiJkEtq6OQw5yQb27y8DzmLRJ7tA/IlxzrnC9V3MFgShGM+5MeazVoWVdr3jAqHV2vsm+INKefUvDjm/buCIxqn9HLuIDash9+hKJRTSaR0GZoRKQVff07v4nnbE0uvhTYoaCR8KcCjk/Mrm4VfmgC8PRFKz9usRfmgQxdUpVZTXfy2aqSlkt4VpFhisjAWeQzzquDH/MsT/EtCuGMZEtngbMUYYItRIBOgZ5qPJ9SMW1JIoraaBRdosLj0bSIXnsGhnS0nAYZN0TrmtBn8ypUGxkMK7KFXhPc2bBoINZxPGeIcw==', [
            'Id' => 'xmldsig-88fbfc45-3be2-4c4a-83ac-0796e1bad4c5-sigvalue'
        ]);

        $signature->KeyInfo(function ($keyInfo) {
            $keyInfo['Id'] = 'xmldsig-87d128b5-aa31-4f0b-8e45-3d9cfa0eec26-keyinfo';
            $keyInfo->X509Data()
                ->X509Certificate('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');
        });

        $signature->Object(function ($object) {
            $qualifyingProperties = $object->add('xades:QualifyingProperties', [
                'xmlns:xades' => 'http://uri.etsi.org/01903/v1.3.2#',
                'xmlns:xades141' => 'http://uri.etsi.org/01903/v1.4.1#',
                'Target' => '#xmldsig-88fbfc45-3be2-4c4a-83ac-0796e1bad4c5'
            ]);
            $signedProperties = $qualifyingProperties->SignedProperties([
                'Id' => 'xmldsig-88fbfc45-3be2-4c4a-83ac-0796e1bad4c5-signedprops'
            ]);
            $signedProperties->SignedSignatureProperties(function ($signedSignatureProperties) {
                $signedSignatureProperties->SigningTime('2016-07-12T11:17:38.639-05:00');
                $signedSignatureProperties->SigningCertificate(function ($signingCertificate) {
                    $signingCertificate->Cert(function ($cert) {
                        $cert->CertDigest(function ($certDigest) {
                            $certDigest->DigestMethod([
                                'Algorithm' => static::SHA1
                            ], static::XML_DSIG);
                            $certDigest->DigestValue(
                                '2el6MfWvYsvEaa/TV513a7tVK0g=',
                                static::XML_DSIG
                            );
                        });
                        $cert->IssuerSerial(function ($issuerSerial) {
                            $issuerSerial->X509IssuerName(
                                'C=CO,L=Bogota D.C.,O=Andes SCD.,OU=Division de certificacion entidad final,CN=CA ANDES SCD S.A. Clase II,1.2.840.113549.1.9.1=#1614696e666f40616e6465737363642e636f6d2e636f',
                                static::XML_DSIG
                            );
                            $issuerSerial->X509SerialNumber(
                                '9128602840918470673',
                                static::XML_DSIG
                            );
                        });
                    });

                    $signingCertificate->Cert(function ($cert) {
                        $cert->CertDigest(function ($certDigest) {
                            $certDigest->DigestMethod([
                                'Algorithm' => static::SHA1
                            ], static::XML_DSIG);
                            $certDigest->DigestValue(
                                'YGJTXnOzmebG2Mc6A/QapNi1PRA=',
                                static::XML_DSIG
                            );
                        });
                        $cert->IssuerSerial(function ($issuerSerial) {
                            $issuerSerial->X509IssuerName(
                                'C=CO,L=Bogota D.C.,O=Andes SCD,OU=Division de certificacion,CN=ROOT CA ANDES SCD S.A.,1.2.840.113549.1.9.1=#1614696e666f40616e6465737363642e636f6d2e636f',
                                static::XML_DSIG
                            );
                            $issuerSerial->X509SerialNumber(
                                '7958418607150926283',
                                static::XML_DSIG
                            );
                        });
                    });

                    $signingCertificate->Cert(function ($cert) {
                        $cert->CertDigest(function ($certDigest) {
                            $certDigest->DigestMethod([
                                'Algorithm' => static::SHA1
                            ], static::XML_DSIG);
                            $certDigest->DigestValue(
                                '6EVr7OINyc49AgvNkie19xul55c=',
                                static::XML_DSIG
                            );
                        });
                        $cert->IssuerSerial(function ($issuerSerial) {
                            $issuerSerial->X509IssuerName(
                                'C=CO,L=Bogota D.C.,O=Andes SCD,OU=Division de certificacion,CN=ROOT CA ANDES SCD S.A.,1.2.840.113549.1.9.1=#1614696e666f40616e6465737363642e636f6d2e636f',
                                static::XML_DSIG
                            );
                            $issuerSerial->X509SerialNumber(
                                '3248112716520923666',
                                static::XML_DSIG
                            );
                        });
                    });
                });
                $signedSignatureProperties->SignaturePolicyIdentifier(function ($signaturePolicyIdentifier) {
                    $signaturePolicyIdentifier->SignaturePolicyId(function ($signaturePolicyId) {
                        $signaturePolicyId->SigPolicyId()
                            ->Identifier('https://facturaelectronica.dian.gov.co/politicadefirma/v1/politicadefirmav1.pdf');
                        $signaturePolicyId->SigPolicyHash(function ($sigPolicyHash) {
                            $sigPolicyHash->DigestMethod([
                                'Algorithm' => static::SHA1
                            ], static::XML_DSIG);
                            $sigPolicyHash->DigestValue(
                                '61fInBICBQOCBwuTwlaOZSi9HKc=',
                                static::XML_DSIG
                            );
                        });
                    });
                });
                $signedSignatureProperties->SignerRole()
                    ->ClaimedRoles()
                    ->ClaimedRole('supplier');
            });

        });

        $this->assertMatchesXmlSnapshot($signature->pretty());
    }
}
