@php
    // the other reviewers' recommendations; empty ones are left out (LEGACY: mscr_Statusfunc.php)
    $others = array_values(array_filter($others, fn ($r) => (string) $r !== ''));
@endphp
<p>Dear Dr. {{ $reviewerName }},</p>
<p>Thank you once again for reviewing manuscript No {{ $manuscript->code_label }} ("{{ $manuscript->title }}") from {{ $authorName }}.
<p>We appreciate your time and effort in reviewing this manuscript and greatly value your assistance as a reviewer for Propagation of Ornamental Plants.</p>
<p>We would like to remind you that you have recommended "{{ $own }}".</p>
@if (count($others) === 2)
<p>The other two reviewers have suggested - "{{ $others[0] }}" and "{{ $others[1] }}".</p>
@elseif (count($others) === 1)
<p>Another reviewer suggested "{{ $others[0] }}".</p>
@endif
<p>Due to above mentioned recommendations we have sent to the corresponding author the attached "Editor's Decision Letter"</p>
<p>With best regards</p>
<p>Prof. Ivan Iliev</p>
<p>Editor-in-Chief</p>
<p>Propagation of Ornamental Plants (www.journal-pop.org)</p>
