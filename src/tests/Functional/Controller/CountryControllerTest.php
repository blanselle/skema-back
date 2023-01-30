<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller;

use App\Entity\Country;
use App\Repository\CountryRepository;
use Symfony\Component\HttpFoundation\Response;

class CountryControllerTest extends AbstractControllerTest
{
    private CountryRepository $countryRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->countryRepository = $this->em->getRepository(Country::class);
    }

    public function testCountryIndexOk(): void
    {
        $this->loginAsAdmin();
        $this->client->request('GET', '/admin/countries');

        $this->assertResponseIsSuccessful();
    }

    public function testCountryNewOk(): void
    {
        $this->loginAsAdmin();
        $this->client->request('POST', '/admin/countries/new');

        $this->assertResponseIsSuccessful();
    }

    public function testCountryEditOk(): void
    {
        $this->loginAsAdmin();
        $this->client->request('GET', '/admin/countries/245/edit');

        $this->assertResponseIsSuccessful();
    }

    public function testCountryDeleteOk(): void
    {
        $this->loginAsAdmin();
        $this->client->request('POST', '/admin/countries/245');

        $this->assertResponseStatusCodeSame(303);
    }
   
    public function testNewSubmitedFailed(): void
    {
        $this->loginAsAdmin();
        $this->client->request('GET', '/admin/countries/new');
        $this->client->submitForm('Sauvegarder', [
            'country[idCountry]' => 'FRA'
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testNewSubmitedOk(): void
    {
        $country = $this->provideCountry();
        $this->loginAsAdmin();
        $this->client->request('GET', '/admin/countries/new');
        $this->checkSubmitForm($country);
        $this->removeCountry($country);
    }

    public function testEditSubmitedOk(): void
    {
        $country = $this->provideCountry();
        $this->saveCountry($country);
        $this->loginAsAdmin();
        $this->client->request('GET', sprintf('/admin/countries/%d/edit', $country->getId()));
        $country = $this->checkSubmitForm($country);
        $this->removeCountry($country);
    }

    private function checkSubmitForm(Country $expectedCountry): Country
    {
        $this->client->submitForm('Sauvegarder', [
            'country[idCountry]' => $expectedCountry->getIdCountry(),
            'country[name]' => $expectedCountry->getName(),
            'country[codeSISE]' => $expectedCountry->getCodeSISE(),
            'country[nationality]' => $expectedCountry->getNationality(),
            'country[active]' => $expectedCountry->getActive()
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_SEE_OTHER);

        $country = $this->countryRepository->findOneByIdCountry($expectedCountry->getIdCountry());

        $this->assertNotNull($country);
        $this->assertSame($expectedCountry->getIdCountry(), $country->getIdCountry());
        $this->assertSame($expectedCountry->getName(), $country->getName());
        $this->assertSame($expectedCountry->getCodeSISE(), $country->getCodeSISE());
        $this->assertSame($expectedCountry->getNationality(), $country->getNationality());
        $this->assertSame($expectedCountry->getActive(), $country->getActive());

        return $country;
    }

    public function testDeleteOk(): void
    {
        $country = $this->provideCountry();
        $this->saveCountry($country);
        $this->loginAsAdmin();
        $this->client->request('GET', sprintf('/admin/countries/%d/edit', $country->getId()));

        $this->client->submitForm(''); // delete button on edit page

        $country = $this->countryRepository->findOneByIdCountry($country->getIdCountry());

        $this->assertNull($country);
    }

    private function saveCountry(Country $country): void
    {
        $this->em->persist($country);
        $this->em->flush();
    }

    private function removeCountry(Country $country): void
    {
        $country = $this->countryRepository->findOneByIdCountry($country->getIdCountry());
        $this->em->remove($country);
        $this->em->flush();
    }

    private function provideCountry(): Country
    {
        return (new Country())
            ->setIdCountry('FRA2')
            ->setName('France2')
            ->setCodeSISE('123')
            ->setNationality('Française')
            ->setActive(true)
        ;
    }
}