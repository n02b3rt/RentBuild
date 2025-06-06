@extends('layouts.app')

@section('content')
    <section class="max-w-[800px] mx-auto px-4 py-12">
        <h1 class="text-4xl font-bold mb-6 text-center">Regulamin Promocji</h1>

        {{-- 1. Postanowienia ogólne --}}
        <div class="mb-10 p-6 bg-white rounded-lg shadow">
            <h2 class="text-2xl font-semibold mb-3">1. Postanowienia ogólne</h2>
            <p class="mb-3 text-gray-700">
                1.1. Niniejszy Regulamin określa zasady, zakres oraz warunki przyznawania i łączenia promocji obowiązujących w serwisie świadczącym usługi wypożyczania sprzętu („Serwis”).
            </p>
            <p class="mb-3 text-gray-700">
                1.2. Promocje opisane w niniejszym Regulaminie mogą dotyczyć:
            </p>
            <ul class="list-disc list-inside mb-3 text-gray-700">
                <li>a) Promocji lojalnościowej,</li>
                <li>b) Promocji pojedynczych produktów,</li>
                <li>c) Promocji na kategorie sprzętu.</li>
            </ul>
            <p class="mb-3 text-gray-700">
                1.3. Wszystkie promocje są przyznawane zgodnie z ustalonymi poniżej zasadami, a ich istotą jest obniżenie ceny wypożyczenia lub zakupu sprzętu (dalej także: „Rabat”).
            </p>
            <p class="mb-3 text-gray-700">
                1.4. Regulamin obowiązuje od momentu jego opublikowania i stosuje się do wszystkich transakcji zawieranych od dnia wejścia regulaminu w życie, chyba że w ramach poszczególnych promocji określono inne daty obowiązywania.
            </p>
            <p class="mb-3 text-gray-700">
                1.5. Administrator Serwisu (dalej: „Administrator”) zastrzega sobie prawo do czasowego zawieszenia, modyfikacji lub zakończenia trwania promocji określonych w niniejszym Regulaminie. Wszelkie zmiany w regulaminie będą publikowane na stronie Serwisu.
            </p>
        </div>

        {{-- 2. Definicje --}}
        <div class="mb-10 p-6 bg-white rounded-lg shadow">
            <h2 class="text-2xl font-semibold mb-3">2. Definicje</h2>
            <p class="mb-3 text-gray-700">
                2.1. <strong>Użytkownik</strong> – osoba fizyczna lub prawna, która dokonuje rejestracji w Serwisie, korzysta z usług Serwisu oraz zawiera transakcje wypożyczenia sprzętu.
            </p>
            <p class="mb-3 text-gray-700">
                2.2. <strong>rentals_count</strong> – pole w Karcie Użytkownika, wskazujące liczbę zakończonych wypożyczeń dokonanych przez Użytkownika.
            </p>
            <p class="mb-3 text-gray-700">
                2.3. <strong>Sprzęt (Equipment)</strong> – przedmiot wypożyczenia lub sprzedaży dostępny w Serwisie, zdefiniowany przez unikalny identyfikator oraz cenę bazową (<code>OriginalPrice</code>).
            </p>
            <p class="mb-3 text-gray-700">
                2.4. <strong>Kategoria sprzętu (category)</strong> – przyporządkowana Użytkownikowi klasyfikacja, np. „Kamery”, „Laptopy” itp., określająca grupę produktów w Serwisie.
            </p>
            <p class="mb-3 text-gray-700">
                2.5. <strong>Promocja lojalnościowa</strong> – promocja naliczana na podstawie liczby zakończonych wypożyczeń (rentals_count) przez Użytkownika.
            </p>
            <p class="mb-3 text-gray-700">
                2.6. <strong>Promocja pojedynczego produktu</strong> – promocja przypisana wyłącznie do konkretnego sprzętu.
            </p>
            <p class="mb-3 text-gray-700">
                2.7. <strong>Promocja na kategorię sprzętu</strong> – promocja przypisana do wszystkich sprzętów w ramach jednej kategorii.
            </p>
            <p class="mb-3 text-gray-700">
                2.8. <strong>Rabat</strong> – procentowa obniżka ceny bazowej sprzętu, wyrażona w wartościach od 0% do 100%.
            </p>
            <p class="mb-3 text-gray-700">
                2.9. <strong>Okres promocyjny</strong> – przedział czasowy wyznaczony przez Administratora, w jakim dana promocja jest aktywna, określony w polach <code>start_datetime</code> (data i godzina rozpoczęcia) oraz <code>end_datetime</code> (data i godzina zakończenia).
            </p>
            <p class="mb-3 text-gray-700">
                2.10. <strong>promotion_type</strong> – pole w modelu <code>Equipment</code>, przyjmujące wartości:
            </p>
            <ul class="list-disc list-inside mb-3 text-gray-700">
                <li>a) <code>‘pojedyncza’</code> – w odniesieniu do promocji przypisanej do konkretnego sprzętu,</li>
                <li>b) <code>‘kategoria’</code> – w odniesieniu do promocji obejmującej wszystkie produkty w danej kategorii,</li>
                <li>c) brak wartości – w przypadku braku aktywnej promocji pojedynczej lub kategorii.</li>
            </ul>
        </div>

        {{-- 3. Promocja lojalnościowa --}}
        <div class="mb-10 p-6 bg-white rounded-lg shadow">
            <h2 class="text-2xl font-semibold mb-3">3. Promocja lojalnościowa</h2>
            <p class="mb-3 text-gray-700">
                3.1. Prawo do skorzystania z Promocji lojalnościowej przysługuje zarejestrowanym Użytkownikom Serwisu.
            </p>
            <p class="mb-3 text-gray-700">
                3.2. Promocja lojalnościowa naliczana jest na podstawie wartości pola <code>rentals_count</code> w Karcie Użytkownika, oznaczającego liczbę zakończonych wypożyczeń, o ile dane wypożyczenie zostało w pełni rozliczone („Zakończone wypożyczenie”).
            </p>
            <p class="mb-3 text-gray-700">
                3.3. Rabaty w ramach Promocji lojalnościowej przyznawane są według następujących reguł:
            </p>
            <ul class="list-disc list-inside mb-3 text-gray-700">
                <li>
                    <strong>Pierwsze wypożyczenie</strong> (rentals_count == 0): rabat w wysokości <span class="font-semibold">20%</span> od ceny bazowej sprzętu.
                </li>
                <li>
                    <strong>Każde piąte wypożyczenie</strong> (tj. rentals_count + 1 jest wielokrotnością 5, lecz różne od 20): rabat w wysokości <span class="font-semibold">25%</span> od ceny bazowej sprzętu.
                </li>
                <li>
                    <strong>Każde dwudzieste wypożyczenie</strong> (tj. rentals_count + 1 jest wielokrotnością 20): rabat w wysokości <span class="font-semibold">50%</span> od ceny bazowej sprzętu.
                </li>
                <li>
                    <strong>Pozostałe przypadki</strong>: brak rabatu.
                </li>
            </ul>
            <p class="mb-3 text-gray-700">
                3.4. Przykład:
            </p>
            <ul class="list-disc list-inside mb-3 text-gray-700">
                <li>Jeżeli Użytkownik ma zakończonych 4 wypożyczenia (rentals_count == 4), to przy kolejnym piątym wypożyczeniu otrzymuje rabat 25%.</li>
                <li>Jeżeli Użytkownik ma zakończonych 19 wypożyczeń (rentals_count == 19), to przy kolejnym dwudziestym wypożyczeniu otrzymuje rabat 50%.</li>
            </ul>
            <p class="mb-3 text-gray-700">
                3.5. Promocja lojalnościowa stosowana jest dopiero w sytuacjach, gdy na dany sprzęt nie obowiązuje Promocja pojedyncza ani Promocja na kategorię (zob. § 6).
            </p>
            <p class="text-gray-500 text-sm">
                3.6. Promocja lojalnościowa jest bezterminowa i nie wymaga odrębnej rejestracji ze strony Użytkownika, pod warunkiem posiadania wymaganej liczby zakończonych wypożyczeń.
            </p>
        </div>

        {{-- 4. Promocje pojedynczych produktów --}}
        <div class="mb-10 p-6 bg-white rounded-lg shadow">
            <h2 class="text-2xl font-semibold mb-3">4. Promocje pojedynczych produktów</h2>
            <p class="mb-3 text-gray-700">
                4.1. Promocja pojedyncza dotyczy wyłącznie konkretnego sprzętu i jest definiowana następującymi parametrami:
            </p>
            <ul class="list-disc list-inside mb-3 text-gray-700">
                <li><strong>start_datetime</strong> – data i godzina rozpoczęcia Promocji pojedynczej,</li>
                <li><strong>end_datetime</strong> – data i godzina zakończenia Promocji pojedynczej,</li>
                <li><strong>discount</strong> – procent rabatu w odniesieniu do ceny bazowej sprzętu,</li>
                <li><strong>promotion_type</strong> – ustawiana w modelu <code>Equipment</code> wartość <code>'pojedyncza'</code>.</li>
            </ul>
            <p class="mb-3 text-gray-700">
                4.2. W przypadku aktywnej Promocji pojedynczej (tj. gdy bieżący czas mieści się w przedziale [<code>start_datetime</code>, <code>end_datetime</code>] włącznie), cena wyświetlana w Serwisie obliczana jest według wzoru:
            </p>
            <pre class="bg-gray-100 p-4 rounded text-sm text-gray-800 mb-3">OriginalPrice × (1 – discount/100)</pre>
            <p class="mb-3 text-gray-700">
                4.3. Po upływie <code>end_datetime</code> Promocja pojedyncza wygasa automatycznie. Wówczas pola <code>promotion_type</code>, <code>start_datetime</code>, <code>end_datetime</code> oraz <code>discount</code> w modelu <code>Equipment</code> zostają wyzerowane (usunięte), a sprzęt wraca do ceny bazowej, o ile nie obowiązuje inna promocja.
            </p>
            <p class="text-gray-500 text-sm">
                4.4. Administrator Serwisu ma wyłączne prawo do definiowania, modyfikowania bądź anulowania Promocji pojedynczych.
            </p>
        </div>

        {{-- 5. Promocje na kategorie sprzętu --}}
        <div class="mb-10 p-6 bg-white rounded-lg shadow">
            <h2 class="text-2xl font-semibold mb-3">5. Promocje na kategorie sprzętu</h2>
            <p class="mb-3 text-gray-700">
                5.1. Promocja na kategorię obejmuje wszystkie sprzęty przypisane do tej samej kategorii (pole <code>category</code> w modelu <code>Equipment</code>).
            </p>
            <p class="mb-3 text-gray-700">
                5.2. Parametry Promocji na kategorię to:
            </p>
            <ul class="list-disc list-inside mb-3 text-gray-700">
                <li><strong>start_datetime</strong> – data i godzina rozpoczęcia promocji,</li>
                <li><strong>end_datetime</strong> – data i godzina zakończenia promocji,</li>
                <li><strong>discount</strong> – procent rabatu,</li>
                <li><strong>promotion_type</strong> – wartość <code>'kategoria'</code> zostaje przypisana do wszystkich sprzętów w określonej kategorii.</li>
            </ul>
            <p class="mb-3 text-gray-700">
                5.3. W okresie trwania Promocji na kategorię (tj. gdy bieżący czas mieści się w przedziale [<code>start_datetime</code>, <code>end_datetime</code>] włącznie), cena wyświetlana we wszystkich miejscach Serwisu (opis produktu, koszyk) dla sprzętów z danej kategorii obliczana jest według wzoru:
            </p>
            <pre class="bg-gray-100 p-4 rounded text-sm text-gray-800 mb-3">NewPrice = OriginalPrice × (1 – discount/100)</pre>
            <p class="mb-3 text-gray-700">
                5.4. Po zakończeniu <code>end_datetime</code> Promocja na kategorię automatycznie wygasa, a w polach <code>promotion_type</code>, <code>start_datetime</code>, <code>end_datetime</code> oraz <code>discount</code> zostają usunięte informacje na temat promocji. Sprzęty wracają do ceny bazowej, o ile nie obowiązuje dla nich Promocja pojedyncza.
            </p>
            <p class="text-gray-500 text-sm">
                5.5. Administrator Serwisu jest jedynym podmiotem uprawnionym do ustanawiania, modyfikowania lub wygaszania Promocji na kategorie.
            </p>
        </div>

        {{-- 6. Łączenie promocji --}}
        <div class="mb-10 p-6 bg-white rounded-lg shadow">
            <h2 class="text-2xl font-semibold mb-3">6. Łączenie promocji</h2>
            <p class="mb-3 text-gray-700">
                6.1. W przypadku, gdy na dany sprzęt mogłyby być aktywne jednocześnie różne typy promocji (pojedyncza, kategoria lub lojalnościowa), obowiązuje następująca kolejność priorytetów:
            </p>
            <ol class="list-decimal list-inside mb-3 text-gray-700">
                <li><strong>Promocja pojedyncza</strong> (najwyższy priorytet).</li>
                <li><strong>Promocja na kategorię</strong> (drugi priorytet).</li>
                <li><strong>Promocja lojalnościowa</strong> (najniższy priorytet).</li>
            </ol>
            <p class="mb-3 text-gray-700">
                6.2. Oznacza to, że:
            </p>
            <ul class="list-disc list-inside mb-3 text-gray-700">
                <li>
                    6.2.a. Jeżeli sprzęt posiada aktywną Promocję pojedynczą, stosuje się wyłącznie jej warunki (wysokość i okres Rabatu) – bez względu na inne promocje.
                </li>
                <li>
                    6.2.b. Jeżeli sprzęt nie posiada aktywnej Promocji pojedynczej, lecz należy do kategorii objętej Promocją na kategorię, stosuje się warunki tej promocji.
                </li>
                <li>
                    6.2.c. Jeżeli ani Promocja pojedyncza, ani Promocja na kategorię nie są aktywne, wówczas rozpatrywana jest możliwość przyznania Promocji lojalnościowej, o ile Użytkownik spełnia wymagania liczby zakończonych wypożyczeń (zob. § 3).
                </li>
                <li>
                    6.2.d. Jeżeli żaden z powyższych mechanizmów nie uprawnia Użytkownika do uzyskania Rabatu, sprzęt sprzedawany jest w cenie bazowej (<code>OriginalPrice</code>).
                </li>
            </ul>
            <p class="mb-3 text-gray-700">
                6.3. Przykład zastosowania kolejności priorytetów:
            </p>
            <ul class="list-disc list-inside mb-3 text-gray-700">
                <li>Produkt „Kamera XYZ” należy do kategorii „Kamery”.</li>
                <li>Administrator wprowadził Promocję na kategorię „Kamery” w wysokości 10% z okresem od 1 czerwca do 7 czerwca.</li>
                <li>Użytkownik ma zakończonych 4 wypożyczenia (rentals_count = 4), uprawniające do uzyskania 25% Rabatu lojalnościowego (na piąte wypożyczenie).</li>
                <li>W dniu 3 czerwca:
                    <ul class="list-disc list-inside ml-5 mb-2 text-gray-700">
                        <li>Jeżeli „Kamera XYZ” ma aktywną Promocję pojedynczą (np. 30% Rabatu od 2 czerwca do 5 czerwca), wówczas stosuje się wyłącznie Rabat promocyjny 30%.</li>
                        <li>Jeżeli „Kamera XYZ” nie ma Promocji pojedynczej, ale kategoria „Kamery” jest objęta 10% Rabatem, Użytkownik płaci cenę obniżoną o 10%.</li>
                        <li>Jeżeli ani Promocja pojedyncza, ani Promocja na kategorię nie są aktywne, wówczas Użytkownik otrzymuje Promocję lojalnościową (25% Rabatu).</li>
                    </ul>
                </li>
            </ul>
        </div>

        {{-- 7. Zasady obsługi promocji --}}
        <div class="mb-10 p-6 bg-white rounded-lg shadow">
            <h2 class="text-2xl font-semibold mb-3">7. Zasady obsługi promocji</h2>
            <p class="mb-3 text-gray-700">
                7.1. Rabat przyznawany jest automatycznie w procesie składania zamówienia lub wypożyczenia, po spełnieniu warunków określonych w niniejszym Regulaminie.
            </p>
            <p class="mb-3 text-gray-700">
                7.2. W widoku koszyka, a także w szczegółach produktu, Użytkownik widzi:
            </p>
            <ul class="list-disc list-inside mb-3 text-gray-700">
                <li>a) cenę bazową sprzętu (<code>OriginalPrice</code>),</li>
                <li>b) wartość i rodzaj przyznanego Rabatu (np. „Rabat lojalnościowy 25%”),</li>
                <li>c) cenę po Rabacie obliczoną zgodnie z kolejnością priorytetów.</li>
            </ul>
            <p class="mb-3 text-gray-700">
                7.3. Po zakończeniu obowiązywania dowolnej promocji (po przekroczeniu <code>end_datetime</code>) pola związane z promocją w modelu <code>Equipment</code> (<code>promotion_type</code>, <code>start_datetime</code>, <code>end_datetime</code>, <code>discount</code>) są automatycznie wyzerowane (usuwane).
            </p>
            <p class="mb-3 text-gray-700">
                7.4. Jeżeli Użytkownik wykupił usługę lub dokonał rezerwacji z zastosowaniem konkretnej promocji, a promocja wygaśnie przed finalizacją transakcji (np. utracenie połączenia, opóźnienie w płatności), zostanie zastosowana promocja o niższym priorytecie lub cena bazowa, jeżeli nie będzie innych aktywnych promocji.
            </p>
            <p class="mb-3 text-gray-700">
                7.5. Każda zmiana statusu promocji (rozpoczęcie, zakończenie, modyfikacja) jest rejestrowana w systemie Serwisu. Użytkownik ma prawo do wglądu w historię swoich promocji w profilu użytkownika.
            </p>
        </div>

        {{-- 8. Postanowienia końcowe --}}
        <div class="p-6 bg-white rounded-lg shadow">
            <h2 class="text-2xl font-semibold mb-3">8. Postanowienia końcowe</h2>
            <p class="mb-3 text-gray-700">
                8.1. Administrator dokłada najwyższych starań, aby informacje o promocjach były zawsze aktualne, jednak nie ponosi odpowiedzialności za opóźnienia w synchronizacji danych prezentowanych w Serwisie.
            </p>
            <p class="mb-3 text-gray-700">
                8.2. W razie wystąpienia okoliczności nadzwyczajnych (awarie techniczne, błąd systemu itp.) Administrator zastrzega sobie prawo do czasowego zawieszenia promocji, bez prawa Użytkownika do odszkodowania.
            </p>
            <p class="mb-3 text-gray-700">
                8.3. Wszelkie spory wynikłe z korzystania z promocji będą rozstrzygane polubownie, a w przypadku braku porozumienia – przez sąd właściwy miejscowo dla siedziby Administratora.
            </p>
            <p class="mb-3 text-gray-700">
                8.4. W sprawach nieuregulowanych niniejszym Regulaminem zastosowanie mają przepisy powszechnie obowiązującego prawa Rzeczypospolitej Polskiej.
            </p>
            <p class="text-gray-500 text-sm">
                8.5. Regulamin wchodzi w życie z dniem jego opublikowania na stronach Serwisu i obowiązuje do odwołania.
            </p>
        </div>
    </section>
@endsection
