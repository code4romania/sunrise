@props([
    'content' => null,
    'colspan' => 1,
])

<tr>
    <td class="bg-white" colspan="{{ $colspan }}">
        <div class="w-full px-3 py-4">
            <div class="text-sm leading-6 text-gray-950 dark:text-white">
                {{ $content }}
            </div>
        </div>
    </td>
</tr>
