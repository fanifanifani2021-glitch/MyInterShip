@extends('templates.simple')

@section('title', 'Security Policy')
@section('page-title', 'Security Policy')

@section('meta-description')
MyInternship security policy, responsible disclosure guidelines, and vulnerability reporting information.
@endsection

@section('content')
<div class="container py-4 security-policy">


    <p>
        At <strong>MyInternship</strong>, we take the security of our systems and user data seriously.
        This document outlines our approach to security and provides guidance for reporting potential
        vulnerabilities responsibly.
    </p>

    <p>
        We appreciate the efforts of security researchers and the community in helping us maintain
        a safe and reliable platform.
    </p>

    <hr>

    <h3>Responsible Disclosure</h3>
    <p>
        If you believe you have discovered a security vulnerability in any part of the MyInternship
        platform, we encourage you to report it privately and responsibly.
    </p>

    <ul>
        <li><strong>Email:</strong> <a href="mailto:security@myinternship.id">security@myinternship.id</a></li>
    </ul>

    <p>Please include the following information in your report:</p>
    <ul>
        <li>Description of the vulnerability</li>
        <li>Steps to reproduce the issue</li>
        <li>Potential impact</li>
        <li>Screenshots or proof-of-concept (if available)</li>
    </ul>

    <hr>

    <h3>Scope</h3>
    <p>This security policy applies to:</p>
    <ul>
        <li>The MyInternship web application</li>
        <li>Public APIs under the <code>myinternship.id</code> domain</li>
        <li>Official subdomains operated by MyInternship</li>
    </ul>

    <p>
        Third-party services integrated into MyInternship are out of scope and should be reported
        directly to their respective providers.
    </p>

    <hr>

    <h3>Out of Scope Activities</h3>
    <p>The following activities are strictly prohibited:</p>
    <ul>
        <li>Denial of Service (DoS / DDoS) attacks</li>
        <li>Brute force attacks</li>
        <li>Social engineering or phishing</li>
        <li>Physical attacks on infrastructure</li>
        <li>Automated scans that degrade service availability</li>
        <li>Accessing or modifying other users’ data</li>
    </ul>

    <p>
        Engaging in these activities may result in legal action.
    </p>

    <hr>

    <h3>Safe Harbor</h3>
    <p>
        MyInternship considers security research conducted in accordance with this policy to be
        authorized. We will not pursue legal action against individuals who:
    </p>

    <ul>
        <li>Act in good faith</li>
        <li>Avoid privacy violations and data destruction</li>
        <li>Report vulnerabilities promptly and privately</li>
        <li>Do not exploit issues beyond what is necessary to demonstrate risk</li>
    </ul>

    <p>
        This safe harbor applies only to activities conducted within the scope of this policy.
    </p>

    <hr>

    <h3>Vulnerability Handling</h3>
    <p>Once a valid vulnerability report is received:</p>
    <ol>
        <li>The report will be reviewed and assessed</li>
        <li>Confirmed issues will be prioritized based on severity</li>
        <li>The reporter may be contacted for clarification</li>
        <li>The issue will be resolved within a reasonable timeframe</li>
    </ol>

    <hr>

    <h3>Security Practices</h3>
    <p>
        MyInternship applies various security best practices, including but not limited to:
    </p>
    <ul>
        <li>Role-based access control</li>
        <li>Input validation and request throttling</li>
        <li>Secure session handling</li>
        <li>Server-side logging and monitoring</li>
        <li>Periodic security reviews</li>
    </ul>

    <p>
        While no system can be guaranteed to be completely secure, we continuously improve
        our security posture.
    </p>

    <hr>

    <h3>Policy Updates</h3>
    <p>
        This policy may be updated periodically. The latest version will always be available
        on the official MyInternship website.
    </p>

    <hr>

    <h3>Contact</h3>
    <p>
        For security-related concerns only, please contact:
    </p>
    <p>
        <strong>Email:</strong>
        <a href="mailto:security@myinternship.id">security@myinternship.id</a>
    </p>

    <p class="text-muted mt-4">
        Last updated: December 2025
    </p>

</div>
@endsection
