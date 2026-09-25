@extends('layouts.app')

@section('title', 'Spolupráce')
@section('description', 'Spolupracujte se studenty Creative Hill College – reklamní spoty, vizuální identita, weby a aplikace na míru.')

@section('content')
<div class="bg-gray-50 min-h-screen py-12">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto">
            <div class="bg-white rounded-xl shadow-md overflow-hidden">
                <div class="p-8">
                    <h1 class="text-4xl font-bold mb-8 text-yellow-500">Spolupráce</h1>

                    <div class="prose max-w-none text-gray-700 mb-12">
                        <p class="mb-6">
                            Ve výuce na Creative Hill College podporujeme moderní výukové metody s využitím digitálních technologií a pomáháme studentům rozvíjet znalosti a dovednosti pro jejich budoucí profesní uplatnění.
                        </p>
                        <p class="mb-6">
                            Nabízíme profesionální tvorbu reklamních spotů, návrh vizuální identity, webové stránky včetně designu, vývoj počítačových programů na míru a další specializované služby.
                        </p>
                        <h2 class="text-2xl font-bold mt-8 mb-4 text-yellow-500">Kontaktní osoby</h2>
                        <p>Chcete s námi spolupracovat? Kontaktujte vedoucího daného zaměření:</p>
                    </div>

                    <!-- Contact Cards -->
                    <div class="space-y-8">
                        <!-- Game Development -->
                        <h2 class="text-xl font-bold mt-8 mb-4 text-black">Vývoj počítačových her a multimediálních aplikací</h2>
                        <div class="flex flex-col md:flex-row gap-6 p-6 bg-gray-50 rounded-lg">
                            <div class="w-32 h-32 flex-shrink-0">
                                <img src="{{ asset('images/defaults/svejda.jpg') }}" alt="Ing. Jaromír Švejda" class="w-full h-full object-cover rounded-lg">
                            </div>
                            <div>
                                <p class="text-lg font-medium">Ing. Jaromír Švejda, Ph.D.</p>
                                <div class="mt-4">
                                    <a href="mailto:jaromir.svejda@creativehill.cz" class="inline-flex items-center text-gray-700 hover:text-yellow-500 transition-colors">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                        </svg>
                                        jaromir.svejda@creativehill.cz
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Multimedia and Digital Design Section -->
                        <h2 class="text-xl font-bold mt-8 mb-4 text-black">Multimédia a digitální design</h2>

                        <!-- First Teacher -->
                        <div class="flex flex-col md:flex-row gap-6 p-6 bg-gray-50 rounded-lg mb-6">
                            <div class="w-32 h-32 flex-shrink-0">
                                <img src="{{ asset('images/defaults/hablova.jpg') }}" alt="MgA. Hana Háblová" class="w-full h-full object-cover rounded-lg">
                            </div>
                            <div>
                                <p class="text-lg font-medium">MgA. Hana Háblová, DiS.</p>
                                <div class="mt-4">
                                    <a href="mailto:hana.hablova@creativehill.cz" class="inline-flex items-center text-gray-700 hover:text-yellow-500 transition-colors">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                        </svg>
                                        hana.hablova@creativehill.cz
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Second Teacher -->
                        <div class="flex flex-col md:flex-row gap-6 p-6 bg-gray-50 rounded-lg">
                            <div class="w-32 h-32 flex-shrink-0">
                                <img src="{{ asset('images/defaults/vecerkova.jpg') }}" alt="Mgr. Barbora Večerková" class="w-full h-full object-cover rounded-lg">
                            </div>
                            <div>
                                <p class="text-lg font-medium">Mgr. Barbora Večerková</p>
                                <div class="mt-4">
                                    <a href="mailto:barbora.vecerkova@creativehill.cz" class="inline-flex items-center text-gray-700 hover:text-yellow-500 transition-colors">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                        </svg>
                                        barbora.vecerkova@creativehill.cz
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Media Production Section -->
                        <h2 class="text-xl font-bold mt-8 mb-4 text-black">Mediální tvorba</h2>
                        <div class="flex flex-col md:flex-row gap-6 p-6 bg-gray-50 rounded-lg">
                            <div class="w-32 h-32 flex-shrink-0">
                                <img src="{{ asset('images/defaults/vacek.jpg') }}" alt="MgA. Jonáš Vacek" class="w-full h-full object-cover rounded-lg">
                            </div>
                            <div>
                                <p class="text-lg font-medium">MgA. Jonáš Vacek</p>
                                <div class="mt-4">
                                    <a href="mailto:jonas.vacek@creativehill.cz" class="inline-flex items-center text-gray-700 hover:text-yellow-500 transition-colors">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                        </svg>
                                        jonas.vacek@creativehill.cz
                                    </a>
                                </div>
                            </div>
                        </div>
                        <!-- Add similar blocks for other contacts -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

            <!-- Footer Section -->
            <div class="mt-12 bg-gray-50 p-8 rounded-xl shadow-md">
                <div class="text-center mb-8">
                    <h3 class="text-2xl font-bold text-black mb-4">Kontaktní údaje školy</h3>
                    <p class="text-lg text-gray-700 mb-6">Nejste si jistí, co zvolit? Kontaktujte nás, společně najdeme tu správnou cestu.</p>
                    <div class="flex flex-col sm:flex-row justify-center items-center gap-6 mb-8">
                        <a href="mailto:info@creativehill.cz" class="flex items-center text-lg text-black-600 hover:text-yellow-700 font-medium">
                            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            info@creativehill.cz
                        </a>
                        <span class="hidden sm:block text-gray-400">|</span>
                        <a href="tel:+420725878303" class="flex items-center text-lg text-black-600 hover:text-yellow-700 font-medium">
                            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            +420 725 878 303
                        </a>
                    </div>
                </div>


            </div>
        </div>
    </div>
</div>
@endsection
