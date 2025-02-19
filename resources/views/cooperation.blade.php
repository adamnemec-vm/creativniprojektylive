@extends('layouts.app')

@section('content')
<div class="bg-gray-100 min-h-screen py-8">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto">
            <div class="bg-white rounded-lg shadow-lg p-8">
                <h1 class="text-3xl font-bold mb-6" style="color: #fed501;">Spolupráce</h1>
                
                <div class="prose max-w-none text-gray-800">
                    <p class="mb-4">
                        Jsme otevřeni novým příležitostem a spolupráci. Pokud máte zajímavý projekt nebo nápad, 
                        neváhejte nás kontaktovat.
                    </p>

                    <h2 class="text-xl font-bold mt-8 mb-4" style="color: #fed501;">Co nabízíme</h2>
                    <ul class="list-disc pl-6 mb-6">
                        <li>Vývoj webových a mobilních aplikací</li>
                        <li>Grafický design a branding</li>
                        <li>Video produkce a postprodukce</li>
                        <li>Konzultace a poradenství</li>
                    </ul>

                    <h2 class="text-xl font-bold mt-8 mb-4" style="color: #fed501;">Kontaktujte nás</h2>
                    <p class="mb-4">
                        Pro více informací o možnostech spolupráce nás můžete kontaktovat:
                    </p>
                    <ul class="list-none space-y-2">
                        <li class="flex items-center">
                            <svg class="w-5 h-5 mr-2" style="color: #fed501;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            <a href="mailto:info@creativehill.cz" class="hover:text-yellow-600 transition-colors">
                                info@creativehill.cz
                            </a>
                        </li>
                        <li class="flex items-center">
                            <svg class="w-5 h-5 mr-2" style="color: #fed501;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            <a href="tel:+420123456789" class="hover:text-yellow-600 transition-colors">
                                +420 123 456 789
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 