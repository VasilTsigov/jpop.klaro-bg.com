@extends('layouts.app')
@section('title', 'Instructions to the authors and reviewers')
@section('content')
<p><strong>To the authors:</strong><br>
  <a href="{{ route('instructions.consent') }}">Consent for authors</a></p>
<p><strong>How to submit manuscript:</strong></p>
<p>1. Go to the journal website: <a href="http://www.journal-pop.org" target="_blank" rel="noopener">www.journal-pop.org</a><br>
2. Click "Submit or review manuscript"<br>
3. Click "Submit manuscript"<br>
4. Fill in <strong>all</strong> boxes indicated by asterisk (*).<br>
- Box "file": Click on the box "browse", select your manuscript (+ abstract and tables) and click "open". The window will be closed. (The manuscript must be submitted as <strong><u>doc file</u></strong>.)<br>
- The photographs and graphs (if you have) must be attached to the indicated separate boxes (The <strong><u>photographs</u></strong> must be <strong>TIF</strong> (or JPEG) files and <strong><u>the scanned image</u></strong> must be <strong><u>minimum 300 dpi</u></strong>. The <strong><u>graphs</u></strong> must be <strong><u>Excel</u></strong> files).<br>
6. Click on "Submit manuscript".<br>
7. You will receive automatic e-mail for successful submission. Please open it and follow the next instruction i.e. fill in, sign, print, and send the consent for authors (the link above) by <strong><u>airmail</u></strong>.</p>
<p><strong>To the reviewer:</strong><br>
  <a href="{{ route('reviews.help', 'instructions') }}">Instructions for reviewer</a><br>
  <a href="{{ route('reviews.help', 'checklist') }}">Checklist to the reviewer</a></p>
<p><strong>How to submit your review:</strong></p>
<p>1. Use the website of the journal: <a href="http://www.journal-pop.org/" target="_blank" rel="noopener">www.journal-pop.org</a><br>
2. Click on “Submit and review manuscript”<br>
3. Open your menu "Manuscript for review"<br>
4. Click on "upload file". A new window will be opened.<br>
5. Click on the first "browse" button (in right) and select the file (manuscript) with the text corrected by you. If you have not any corrections on the text, please attach the manuscript of the author (original manuscript). Then click "open". The window will be closed.<br>
6. Click on the second "browse" button (it is "checklist completed"), select your completed checklist (the link above), and click "open". The window will be closed.<br>
7. Click on the box "select recommendation" and select one of the options.<br>
8. Click on "upload file".</p>
@endsection
