---
topic: Introducing RFCs
start-date: 2023-08-08
author: Animesh Sahu
co-authors: Aditya Yadav, More TBD
---

# Summary
[summary]: #summary

Different people knows different things, which may lead to healthy discussions. And its always better to form a consensus and reach to a point everybody agrees on.

This is for introducing RFCs (Request for Comments) - an open discussion for encouraging initiatives & better decision making in Axios, The technical society of IIIT Lucknow.

# Motivation
[motivation]: #motivation

We have gone through various problems selecting _free slot for classes_, and _event management_.

Our current decision-making process faces issues with excessive noise in public text channels (Whatsapp group), leading to unclear conversations and difficulty in revisiting decision context. To address this, we propose adopting an RFC system. This system provides dedicated spaces for specific proposals, organizing discussions and minimizing noise.


It also help to solve another major part of this problem to collect feedback from the student body. A RFC based system allows for interested students to leave comments in topic which concerns them rather than the approach of having question posted in public text channels where a important decision or topic is drowned out by the noise created by usual conversation.

Previous Failed attempts to fix similar problem:

- [College Gate App](https://play.google.com/store/apps/details?id=com.iiitl.college_gate) (by Anu/Jagnik) introduced way to immediately log campus in-out records.
- [IIITL Wiki](https://wiki.iiitl.ac.in) (by Pranav) introduction of wiki pages for college use.
- Intro to discord server (by Animesh and Karthik S) for management of resources and college information.
- Intro to [College Management Portal](https://github.com/iiitl/college-management) webapp (by Karthik S).

These attempts should have been somehow validated/discussed/slightly-changed before taken into consideration, in order to increase the likelihood that they'll actually be adopted.


# Proposed Solution
[proposed-solution]: #proposed-solution

Introduction to RFCs!

This will reduce the wing differences and create a common place for taking an initiative.

## What is an RFC?
[what-is-an-rfc]: #what-is-an-rfc

The "RFC" (request for comments) is a process to taking inputs from public in any matter that will substantially change or add in to the way the things currently work.

Formally, a simple & crisp design document in markdown is submitted as a PR by an author, and everybody is RFC'd (requested for comments) in that PR and the author is supposed to update the document as discussion moves forwards...

Throughout, a finalization happens, in that case RFC is either merged or rejected.

## Why do you need to do this
[why-do-you-need-to-do-this]: #why-do-you-need-to-do-this

You are always _choosing_, every moment of the day. You'll take a lot of decisions as a part of technical society and witness even more being made. Decisions ranging from minor matters like determining the format of communication or organizing events, to more impactful ones that could significantly shape the way thing currently work.

Recalling the reason behind a decision can be painfully hard after months of ongoing efforts, often because you're missing the context that led up to it. Furthermore, the lack of documentation also makes onboarding next batch and handover processes more difficult as you might find yourself struggling to explain decisions made in the past. Especially when wings are growing and a lot of progress has already been made previously, this will become strikingly obvious.

## Raising and writing an RFC
[raising-and-writing-an-rfc]: #raising-and-writing-an-rfc

In short, to get a major reform happen in the Axios, one must first get the RFC merged into the RFC repo as a markdown file. At that point the RFC is 'active' and may be implemented with the goal of eventual inclusion into Axios.

### The Process
[the-process]: #the-process

1.  Work on your proposal in a Markdown file based on the template (`0000-template.md`) found in this repo.

    - Fork this repo.
    - Create your proposal as `rfcs/0000-my-feature.md` (where "my-feature" is descriptive).
    - Submit a pull request.

2.  Build consensus.

    - RFC will be discussed, everybody is free to put their opinion, as much as possible in the comment thread of the pull request itself.
    - Author will actively look for comments, reply and after fruitful discussion update the rfc as required.

3.  Finalization

    - A team of 3-4 members, who actively participated or are most familiar with the topic guide the discussion and help out author in making informed decisions.
    - A senior member or higher will merge the PR, at which point the RFC will become 'active'.

## Taking part in an RFC
[taking-part-in-an-rfc]: #taking-part-in-an-rfc

Quite simple, go to the 'Files changed' section in the PR, and based on your views add comments to conflicting lines from your viewpoint.


# Drawbacks
[drawbacks]: #drawbacks

What are the disadvantages of doing this?

* Hard to implement, a formal process is resistive by itself.
* May be beneficial for big decisions, but small decisions may resolve by itself with small talk over chatting platforms.
* Some members may not actively participate in the RFC process, leading to potential exclusion of valuable opinions or ideas.
* The need for continuous updating and maintaining RFCs might increase the workload for both authors and reviewers.


# Alternatives
[alternatives]: #alternatives

### What other designs have been considered?

* WhatsApp / Telegram group creation: Informal discussion groups might lead to quick decisions, but they might not be suitable for significant decisions.

* Notion: Using tools like Notion for documentation and collaboration can be useful, but it may lack a formalized process and might not foster as much community involvement and transparency as RFCs.

* Discord forums: This adds up initial adapting resistance, last time it was attempted to maintain all the calendar, mess, extra resource and discussions over there, but failed miserably due to lack of participation with overall complexity and since it wasn't accepted by everyone.

### What is the impact of not doing this?

* Without this, big decisions keep revolving, and usually either keep postponing or go inefficient every year.
* Some problems specially design problems, such as finding slots for taking classes or event management are highly effective to be solved by gathering and discussing the differences between each other's solutions, will be left untouched.
* Lack of a structured decision-making process might lead to conflicts, misunderstandings, and difficulties in reaching a consensus.
* The historical context and rationale behind decisions might not be adequately documented, leading to challenges during onboarding next batch and succession planning.


# Unresolved questions
[unresolved]: #unresolved-questions

1. How can we ensure active participation from all members in the RFC process to avoid potential exclusion of valuable insights?
2. What measures can be taken to streamline the RFC process and prevent it from becoming too time-consuming?
3. How do we strike a balance between using RFCs for significant decisions while allowing small decisions to be made efficiently through informal channels?

