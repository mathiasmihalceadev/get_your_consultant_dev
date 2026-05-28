<?php

namespace Database\Seeders;

use App\Models\Settings;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'mathias.mihalcea1@gmail.com'],
            [
                'name' => 'Mathias',
                'password' => Hash::make('mathyas1234'),
            ]
        );

        User::firstOrCreate(
            ['email' => 'admin@getyourconsultant.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('parola1234'),
            ]
        );

        $this->seedSettingIfMissing('rental_living_ro', $this->rentalLivingPromptRo());
        $this->seedSettingIfMissing('rental_living_eng', $this->rentalLivingPromptEng());
        $this->seedSettingIfMissing('buying_living_ro', $this->buyingLivingPromptRo());
        $this->seedSettingIfMissing('buying_living_eng', $this->buyingLivingPromptEng());
        $this->seedSettingIfMissing('auto_send', false);
    }

    private function seedSettingIfMissing(string $key, mixed $value): void
    {
        if (Settings::query()->where('key', $key)->exists()) {
            return;
        }

        Settings::set($key, $value);
    }

    private function rentalLivingPromptEng(): string
    {
        return $this->buildPrompt(
            'English',
            'Analyze the listing as a residential rental opportunity for someone looking to live in the property.',
            'rental_eng.json'
        );
    }

    private function buyingLivingPromptEng(): string
    {
        return $this->buildPrompt(
            'English',
            'Analyze the listing as a residential purchase opportunity for an owner-occupier, while also covering the investment angle present in the sample structure.',
            'buying_eng.json'
        );
    }

    private function rentalLivingPromptRo(): string
    {
        return $this->buildPromptRo(
            'Analizează anunțul ca oportunitate de închiriere rezidențială pentru locuire.',
            'rental_ro.json'
        );
    }

    private function buyingLivingPromptRo(): string
    {
        return $this->buildPromptRo(
            'Analizează anunțul ca oportunitate de cumpărare rezidențială pentru locuire, păstrând și unghiul investițional existent în structura exemplului.',
            'buying_ro.json'
        );
    }

    private function buildPrompt(string $languageName, string $goal, string $sampleFile): string
    {
        $schemaExample = $this->loadSampleJson($sampleFile);

        return <<<PROMPT
You are an AI real-estate analyst. Analyze the property listing available at the provided URL.

{$goal}

Rules:
- Return strictly valid JSON only.
- Use {$languageName} for every human-readable value in the JSON.
- Preserve the exact overall structure from the schema example below.
- Keep the same top-level keys, nested keys, object nesting, chart ids, chart types, section ids, and array/object shapes.
- Replace the example values with values inferred from the listing.
- If information is missing, estimate a realistic value and keep the expected data type.
- page_one.badges must fit within 2 rows on the first page, so keep only the most decision-relevant badges and never exceed 8 badges.
- page_one.verdict.ideal_for must contain at most 3 short items.
- page_one.verdict.not_ideal_for must contain at most 3 short items.
- If the chart id is total_acquisition_cost, every data.segments.label must be a single word.
- Do not add markdown, explanations, comments, or extra text.

Schema example to follow exactly:
{$schemaExample}
PROMPT;
    }

    private function buildPromptRo(string $goal, string $sampleFile): string
    {
        $schemaExample = $this->loadSampleJson($sampleFile);

        return <<<PROMPT
Ești un analist AI de real-estate. Analizează anunțul imobiliar disponibil la URL-ul primit.

{$goal}

Reguli:
- Returnează strict JSON valid și nimic altceva.
- Toate valorile lizibile pentru utilizator trebuie să fie în limba română.
- Păstrează exact structura generală din exemplul de mai jos.
- Menține aceleași chei de top, chei imbricate, nivele de obiecte, id-uri de grafice, tipuri de grafice, id-uri de secțiuni și forme de array/object.
- Înlocuiește valorile de exemplu cu valori deduse din anunț.
- Dacă lipsesc informații, estimează o valoare realistă și păstrează tipul de date așteptat.
- page_one.badges trebuie să încapă în 2 rânduri pe prima pagină, deci păstrează doar badge-urile cele mai relevante și nu depăși 8 badge-uri.
- page_one.verdict.ideal_for trebuie să conțină maximum 3 elemente scurte.
- page_one.verdict.not_ideal_for trebuie să conțină maximum 3 elemente scurte.
- Dacă graficul are id-ul total_acquisition_cost, fiecare data.segments.label trebuie să fie dintr-un singur cuvânt.
- Nu adăuga markdown, explicații, comentarii sau text suplimentar.

Exemplu de schemă de urmat întocmai:
{$schemaExample}
PROMPT;
    }

    private function loadSampleJson(string $sampleFile): string
    {
        $path = storage_path('app/' . $sampleFile);

        if (!is_file($path)) {
            throw new \RuntimeException("Missing sample JSON for prompt seeding: {$sampleFile}");
        }

        $content = file_get_contents($path);

        if ($content === false) {
            throw new \RuntimeException("Unable to read sample JSON for prompt seeding: {$sampleFile}");
        }

        return trim($content);
    }
}
