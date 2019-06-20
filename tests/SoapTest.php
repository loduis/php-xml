<?php

declare(strict_types=1);

namespace XML\Tests;

use XML\Element;

const WSDL = 'https://vpfe-hab.dian.gov.co/WcfDianCustomerServices.svc';
const WSS_XTP = 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-x509-token-profile-1.0';
const C14N_EXC = 'http://www.w3.org/2001/10/xml-exc-c14n#';
const SOAP = 'http://www.w3.org/2003/05/soap-envelope';
const WCF = 'http://wcf.dian.colombia';
const WSSE = 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd';
const WSU = 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd';
const WSA = 'http://www.w3.org/2005/08/addressing';
const DS = 'http://www.w3.org/2000/09/xmldsig#';
const DIGEST_ALGORITHM = 'http://www.w3.org/2001/04/xmlenc#sha512';
const KEY_ALGORITHM = 'http://www.w3.org/2001/04/xmldsig-more#rsa-sha512';
const WSDL_ACTION = 'http://wcf.dian.colombia/IWcfDianCustomerServices/GetStatus';
const WSS_SMS = 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-soap-message-security-1.0';

class SoapTest extends TestCase
{
    public function testShouldCreateSoapXml()
    {
        $soap = Element::create('soap:Envelope', [
            'xmlns:soap' => SOAP,
            'xmlns:wcf' => WCF
        ]);

        $soap->Header([
            'xmlns:wsa' => WSA
        ], function ($header) {
            $security = $header->add('wsse:Security', [
                'xmlns:wsse' => WSSE,
                'xmlns:wsu'=> WSU
            ]);
            $header->add('wsa:Action', WSDL_ACTION);

            $to = $header->add('wsa:To', [
                'wsu:Id' => 'id-A6E048B0EA1EEA256E155845316230310'
            ], WSDL);

            $security->add('wsu:Timestamp', [
                'wsu:Id' => 'TS-A6E048B0EA1EEA256E155845316231412'
            ], function ($timestamp) {
                $timestamp->Created('2019-05-21T15:39:22.314Z');
                $timestamp->Expires('2019-05-22T08:19:22.314Z');
            });
            $security->BinarySecurityToken('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', [
                'EncodingType' => WSS_SMS . '#Base64Binary',
                'ValueType' => WSS_XTP . '#X509v3',
                'wsu:Id' => 'X509-A6E048B0EA1EEA256E15584531623037'
            ]);
            $security->add('ds:Signature', [
                'Id' => 'SIG-A6E048B0EA1EEA256E155845316230511',
                'xmlns:ds' => DS
            ], function ($signature) use ($to) {
                $signedInfo = $signature->SignedInfo(function ($element) use ($to) {
                    $element->CanonicalizationMethod([
                        'Algorithm' => C14N_EXC
                    ], function ($element) {
                        $element->add('ec:InclusiveNamespaces', [
                            'PrefixList' => 'wsa soap wcf',
                            'xmlns:ec' => C14N_EXC
                        ]);
                    });
                    $element->SignatureMethod([
                        'Algorithm' => KEY_ALGORITHM
                    ]);
                    $element->Reference([
                        'URI' => '#id-A6E048B0EA1EEA256E155845316230310'
                    ], function ($element) use ($to) {
                        $element->Transforms(function ($element) {
                            $element->Transform([
                                'Algorithm' => C14N_EXC
                            ], function ($element) {
                                $element->add('ec:InclusiveNamespaces', [
                                    'xmlns:ec' => C14N_EXC,
                                    'PrefixList' => 'soap wcf',
                                ]);
                            });
                        });
                        $element->DigestMethod([
                            'Algorithm' => DIGEST_ALGORITHM
                        ]);
                        $element->DigestValue('DhfA6l2Pp6KuwxVP3180ULydoCC6HOUCPfj+a61/MrykR4z6hFyP+UU21x46B38BuQNO7VHSFOZJmnuoB7yYHA==');
                    });
                });
                $signature->SignatureValue('ihVIcQsfhSLw55+GFAxYIGFZJUBsV0mbxjW5fN1a1VpiwaaHXFWzw10LcbCOVGKpOmALdQa4I/3hc/PIbMhXySIuQss10vHWLEahgQNksPyAKJ532DH+Qd1Ih4DF/I2CLyYEYlSIGmdD6WMblVtLrLjB[\n]PbdUEUbDGmyKGWoXps19qixq0WVoUfGARj/+6O/xF1IwbYFy9oI5Y+iH2tF3S90WykZ3QNPZrsCtHJIc3aT5QIfUvJUt7KU2qC6QJudoqGH58pQvWFXktpa9nRq4BucMDo9KMxyIhO1M7Xt6b8NP/gXtnv7o4CYCgafvC7P4CBPHpR44UsnoSQyBpQwWUA==');
                $signature->KeyInfo([
                    'Id' => 'KI-A6E048B0EA1EEA256E15584531623038'
                ], function ($element) {
                    $element->add('wsse:SecurityTokenReference', [
                        'wsu:Id' => 'STR-A6E048B0EA1EEA256E15584531623039',
                    ],  function ($element) {
                        $element->Reference([
                            'URI' => '#X509-A6E048B0EA1EEA256E15584531623037',
                            'ValueType' => WSS_XTP . '#X509v3'
                        ]);
                    });
                });
            });
        });

        $soap->Body(function ($body) {
            $body->add('wcf:GetStatus', function ($status) {
                $status->trackId('8763f78ccd241063615affd49580564df2986c07');
            });
        });

        $this->assertMatchesXmlSnapshot($soap->pretty());
    }
}
